<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

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

        // Status options for dropdown in order list
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
            'orderItems.variant'
        ]);

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

        return view('admin.orders.show', compact(
            'order',
            'statuses',
            'paymentStatuses',
            'timeline'
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
            // Update status
            $order->update([
                'order_status' => $request->status,
                'status_notes' => $request->notes
            ]);

            // Check if request is AJAX (for order list page)
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order status updated successfully',
                    'new_status' => $request->status,
                    'new_status_label' => ucfirst($request->status)
                ]);
            }

            // For non-AJAX requests (detail page)
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'new_status' => $request->status
            ]);

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

        $order->update([
            'payment_status' => $validated['payment_status'],
            'payment_notes' => $validated['notes'] ?? null
        ]);

        return redirect()->back()
            ->with('success', 'Payment status updated successfully');
    }

     public function generateInvoice($orderId)
{
    try {
        $order = Order::with(['items'])->findOrFail($orderId);

        if ($order->items->isEmpty()) {
            throw new \Exception("Order contains no items");
        }

        // Prepare addresses
        $billingAddress = implode(', ', array_filter([
            $order->billing_address_line_1,
            $order->billing_address_line_2,
            $order->billing_zip_code
        ]));

        $shippingAddress = implode(', ', array_filter([
            $order->shipping_address_line_1,
            $order->shipping_address_line_2,
            $order->shipping_zip_code
        ]));

        // Format items according to your DB structure
        $formattedItems = $order->items->map(function($item) {
            return [
                'product_name' => $item->product_name,
                'variant' => $item->selected_color.'/Size '.$item->selected_size,
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
                'name' => $order->billing_first_name.' '.$order->billing_last_name,
                'email' => $order->billing_email ?? 'N/A',
                'phone' => $order->billing_phone,
                'address' => $billingAddress ?: 'Address not specified'
            ],
            'shipTo' => [
                'name' => $order->shipping_first_name.' '.$order->shipping_last_name,
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

    /**
     * Generate order timeline.
     */
    protected function getOrderTimeline(Order $order): array
    {
        $timeline = [];

        $timeline[] = [
            'date' => $order->created_at,
            'event' => 'Order placed',
            'description' => 'Order #'.$order->order_number.' has been placed'
        ];

        if ($order->payment_status === 'paid') {
            $timeline[] = [
                'date' => $order->updated_at,
                'event' => 'Payment received',
                'description' => 'Payment has been confirmed'
            ];
        }

        foreach ($order->histories as $history) {
            $timeline[] = [
                'date' => $history->created_at,
                'event' => ucfirst($history->status),
                'description' => $history->notes
            ];
        }

        usort($timeline, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return $timeline;
    }
}
