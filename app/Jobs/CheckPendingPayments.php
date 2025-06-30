<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Midtrans\Transaction;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pendingOrders = Order::where('payment_status', 'pending')
            ->where('created_at', '>', now()->subDays(1))
            ->with('orderItems')
            ->get();

        foreach ($pendingOrders as $order) {
            try {
                // Dapatkan status dari Midtrans
                $status = Transaction::status($order->order_number);

                // Map transaction status ke payment status
                $paymentStatus = $this->mapTransactionStatus(
                    $status->transaction_status,
                    $status->fraud_status ?? null
                );

                // Jika status berubah
                if ($order->payment_status !== $paymentStatus) {
                    \DB::transaction(function () use ($order, $paymentStatus, $status) {
                        // Update status order
                        $order->update([
                            'payment_status' => $paymentStatus,
                            'order_status' => $this->mapOrderStatus($paymentStatus),
                            'midtrans_transaction_id' => $status->transaction_id ?? null,
                            'midtrans_payment_type' => $status->payment_type ?? null,
                            'midtrans_payment_time' => $status->settlement_time ?? null,
                            'midtrans_gross_amount' => $status->gross_amount ?? null,
                        ]);

                        // Jika pembayaran berhasil, update stok dan clear cart
                        if ($paymentStatus === 'paid') {
                            $this->updateStockAndClearCart($order);
                        }
                    });

                    Log::info("Updated order {$order->id} to status {$paymentStatus}");
                }
            } catch (\Exception $e) {
                Log::error("Error checking payment for order {$order->id}: " . $e->getMessage());
                continue;
            }
        }
    }

    /**
     * Map Midtrans transaction status to our payment status
     */
    protected function mapTransactionStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                return ($fraudStatus === 'accept') ? 'paid' : 'pending';
            case 'settlement':
                return 'paid';
            case 'pending':
                return 'pending';
            case 'deny':
                return 'failed';
            case 'expire':
                return 'expired';
            case 'cancel':
                return 'cancelled';
            default:
                return 'failed';
        }
    }

    /**
     * Map payment status to order status
     */
    protected function mapOrderStatus($paymentStatus)
    {
        switch ($paymentStatus) {
            case 'paid':
                return 'processing';
            case 'pending':
                return 'pending';
            case 'failed':
            case 'expired':
            case 'cancelled':
                return 'cancelled';
            default:
                return 'pending';
        }
    }

    /**
     * Update product stock and clear user's cart
     */
    protected function updateStockAndClearCart($order)
    {
        try {
            \DB::transaction(function () use ($order) {
                foreach ($order->orderItems as $item) {
                    $productVariant = \App\Models\ProductVariant::where('product_id', $item->product_id)
                        ->when($item->selected_size, function ($query, $size) {
                            $sizeId = \App\Models\Size::where('name', $size)->value('id');
                            $query->where('size_id', $sizeId);
                        })
                        ->when($item->selected_color, function ($query, $color) {
                            $colorId = \App\Models\Color::where('name', $color)->value('id');
                            $query->where('color_id', $colorId);
                        })
                        ->first();

                    if ($productVariant && $productVariant->stock >= $item->quantity) {
                        $productVariant->decrement('stock', $item->quantity);
                        Log::info("Stock reduced for variant {$productVariant->id} by {$item->quantity}");
                    } elseif ($productVariant) {
                        Log::error("Insufficient stock for variant {$productVariant->id}");
                    }
                }

                // Clear cart
                \App\Models\Cart::where('user_id', $order->user_id)->delete();
                Log::info("Cart cleared for user {$order->user_id}");
            });
        } catch (\Exception $e) {
            Log::error("Error updating stock for order {$order->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
