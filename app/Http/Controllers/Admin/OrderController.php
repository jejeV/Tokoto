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

    /**
     * Export orders to PDF.
     */
    public function exportPdf(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);

        $pdf = PDF::loadView('admin.orders.export', compact('order'));

        return $pdf->download("order-{$order->order_number}.pdf");
    }

    /**
     * Export orders to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
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
