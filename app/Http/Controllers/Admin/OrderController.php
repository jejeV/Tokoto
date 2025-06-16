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

        return view('admin.orders.index', compact('orders', 'statistics'));
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
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                'order_status' => $validated['status'],
                'status_notes' => $validated['notes'] ?? null
            ]);

            // Add to order history
            $order->histories()->create([
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? 'Status updated by admin',
                'user_id' => auth()->id()
            ]);
        });

        return redirect()->back()
            ->with('success', 'Order status updated successfully');
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

        // Order created
        $timeline[] = [
            'date' => $order->created_at,
            'event' => 'Order placed',
            'description' => 'Order #'.$order->order_number.' has been placed'
        ];

        // Payment status changes
        if ($order->payment_status === 'paid') {
            $timeline[] = [
                'date' => $order->updated_at,
                'event' => 'Payment received',
                'description' => 'Payment has been confirmed'
            ];
        }

        // Add status histories
        foreach ($order->histories as $history) {
            $timeline[] = [
                'date' => $history->created_at,
                'event' => ucfirst($history->status),
                'description' => $history->notes
            ];
        }

        // Sort timeline by date
        usort($timeline, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return $timeline;
    }
}
