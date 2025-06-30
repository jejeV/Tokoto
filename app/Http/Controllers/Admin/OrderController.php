<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        $orders = Order::with(['customer', 'orderItems'])
            ->latest()
            ->paginate(10);

        // Count orders by status
        $statistics = [
            'pending' => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        $statusOptions = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return view('partials.admin.orders', compact('orders', 'statistics', 'statusOptions'));
    }

    /**
     * Display order details.
     */
    public function show(Order $order)
    {
        $order->load([
            'customer',
            'orderItems.product',
            'orderItems.variant',
            'histories.user'
        ]);

        $relatedOrders = collect();
        if ($order->customer_id) {
            $relatedOrders = Order::where('customer_id', $order->customer_id)
                                    ->where('id', '!=', $order->id)
                                    ->latest()
                                    ->limit(5)
                                    ->get();
        }

        $statuses = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        $paymentStatuses = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];

        $timeline = $this->getOrderTimeline($order);

        return view('partials.admin.orderdetail', compact(
            'order',
            'statuses',
            'paymentStatuses',
            'timeline',
            'relatedOrders'
        ));
    }

    /**
     * Update order status - modified to work with AJAX for order list page.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        try {
            $oldStatus = $order->order_status;

            // Update status pesanan
            $order->update([
                'order_status' => $request->status,
                'status_notes' => $request->notes
            ]);

            if ($oldStatus !== $request->status) {
                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => $request->status,
                    'notes' => $request->notes ?? 'Status pesanan diubah menjadi ' . ucfirst($request->status),
                    'user_id' => auth()->id(),
                    'additional_data' => ['old_status' => $oldStatus]
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order status updated successfully',
                    'new_status' => $request->status,
                    'new_status_label' => ucfirst($request->status)
                ]);
            }

            return redirect()->back()
                ->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string|max:500'
        ]);

        $oldPaymentStatus = $order->payment_status;

        $order->update([
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['notes'] ?? null
        ]);

        if ($oldPaymentStatus !== $validated['payment_status']) {
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => 'Payment: ' . ucfirst($validated['payment_status']), // Contoh: "Payment: Paid"
                'notes' => $validated['notes'] ?? 'Status pembayaran diubah dari ' . ucfirst($oldPaymentStatus) . ' menjadi ' . ucfirst($validated['payment_status']),
                'user_id' => auth()->id(),
                'additional_data' => ['old_payment_status' => $oldPaymentStatus]
            ]);
        }

        return redirect()->back()
            ->with('success', 'Payment status updated successfully');
    }

    /**
     * Handle order refund.
     */
    public function refund(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $order->total_amount,
            'refund_reason' => 'required|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($order, $request) {
                $oldPaymentStatus = $order->payment_status;

                $order->update([
                    'payment_status' => 'refunded',
                    'refund_amount' => $request->refund_amount,
                    'payment_notes' => ($order->payment_notes ? $order->payment_notes . "\n" : '') . "Refund reason: " . $request->refund_reason
                ]);

                OrderHistory::create([
                    'order_id' => $order->id,
                    'status' => 'Payment: Refunded',
                    'notes' => 'Order refunded for amount ' . $request->refund_amount . '. Reason: ' . $request->refund_reason,
                    'user_id' => auth()->id(),
                    'additional_data' => [
                        'old_payment_status' => $oldPaymentStatus,
                        'refund_amount' => $request->refund_amount
                    ]
                ]);
            });

            return redirect()->back()->with('success', 'Order has been successfully refunded.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        try {
            DB::transaction(function () use ($order) {
                $order->orderItems()->delete();
                $order->histories()->delete();
                $order->delete();
            });

            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function generateInvoice($orderId)
    {
        try {
            $order = Order::with(['orderItems', 'customer'])->findOrFail($orderId);

            if ($order->orderItems->isEmpty()) {
                throw new \Exception("Order contains no items");
            }

            // Prepare addresses
            $billingAddress = implode(', ', array_filter([
                $order->billing_address_line_1,
                $order->billing_address_line_2,
                $order->billing_city,
                $order->billing_state,
                $order->billing_postcode,
                $order->billing_country
            ]));

            $shippingAddress = implode(', ', array_filter([
                $order->shipping_address_line1,
                $order->shipping_address_line2,
                $order->shipping_city,
                $order->shipping_state,
                $order->shipping_postcode,
                $order->shipping_country
            ]));

            $formattedItems = $order->orderItems->map(function($item) {
                $variantDetails = '';
                if ($item->variant) {
                    $variantDetails = 'Variant: ' . $item->variant->name;
                } elseif ($item->selected_color || $item->selected_size) {
                    $details = [];
                    if ($item->selected_color) $details[] = 'Color: ' . $item->selected_color;
                    if ($item->selected_size) $details[] = 'Size: ' . $item->selected_size;
                    $variantDetails = implode(' / ', $details);
                }

                return [
                    'product_name' => $item->product_name,
                    'variant' => $variantDetails,
                    'quantity' => $item->quantity,
                    'price' => $item->price_per_item,
                    'total' => $item->total_price
                ];
            });

            $data = [
                'invoiceNo' => $order->order_number,
                'invoiceDate' => $order->created_at->format('M jS, Y'),
                'dueDate' => $order->created_at->addDays(30)->format('M jS, Y'),
                'from' => [
                    'company' => 'Shoebaru',
                    'name' => 'Shoebaru',
                    'email' => 'shoebaru@gmail.com',
                    'phone' => '+6281819919',
                    'website' => 'shobaru.com',
                    'address' => 'Jln Pinang 9A, Depok Jawa Barat'
                ],
                'billTo' => [
                    'name' => ($order->customer->name ?? $order->billing_first_name.' '.$order->billing_last_name),
                    'email' => $order->billing_email ?? 'N/A',
                    'phone' => $order->billing_phone,
                    'address' => $billingAddress ?: 'Address not specified'
                ],
                'shipTo' => [
                    'name' => ($order->shipping_first_name.' '.$order->shipping_last_name),
                    'phone' => $order->shipping_phone_number,
                    'address' => $shippingAddress ?: $billingAddress
                ],
                'trackingNo' => 'ROB'.str_pad($order->id, 8, '0', STR_PAD_LEFT),
                'items' => $formattedItems,
                'subtotal' => $order->subtotal_amount,
                'discount' => $order->discount_amount,
                'shipping' => $order->shipping_cost,
                'total' => $order->total_amount,
                'paymentMethod' => $order->payment_method,
                'paymentStatus' => $order->payment_status,
                'notes' => 'Thank you for your business!'
            ];

            if (!view()->exists('partials.admin.invoice')) {
                throw new \Exception("Invoice template not found");
            }

            return PDF::loadView('partials.admin.invoice', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ])
                ->download('invoice-'.$order->order_number.'.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invoice generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

// app/Http/Controllers/Admin/OrderController.php
public function exportExcel()
{
    return Excel::download(new OrdersExport(), 'orders_'.now()->format('Ymd_His').'.xlsx');
}

    /**
     * Generate order timeline.
     */
    protected function getOrderTimeline(Order $order): array
    {
        $timeline = [];

        $timeline[] = [
            'date' => $order->created_at,
            'event' => 'Order placed',
            'description' => 'Order #' . $order->order_number . ' has been placed.',
            'user' => null
        ];

        if ($order->relationLoaded('histories')) {
            foreach ($order->histories as $history) {
                $timeline[] = [
                    'date' => $history->created_at,
                    'event' => ucfirst($history->status),
                    'description' => $history->notes,
                    'user' => $history->user ? $history->user->name : 'System'
                ];
            }
        }

        usort($timeline, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return $timeline;
    }
}
