<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Color;
use App\Models\Province;
use App\Models\City;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    protected function configureMidtrans()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function showCheckoutForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melanjutkan checkout.');
        }

        $user = Auth::user();
        $cartItems = $user->cartItems()->with(['product', 'productVariant.size', 'productVariant.color'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $checkoutItems = $cartItems->map(function ($item) {
            $productName = $item->product->name ?? 'Produk Tidak Dikenal';

            $variantDetails = collect([
                $item->productVariant->size->name ?? null,
                $item->productVariant->color->name ?? null
            ])->filter()->implode(', ');

            if (!empty($variantDetails)) {
                $productName .= " ($variantDetails)";
            }

            return [
                'name' => $productName,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total_price' => $item->quantity * $item->price,
            ];
        });

        $cartSubtotal = $checkoutItems->sum('total_price');

        return view('checkout', compact('user', 'checkoutItems', 'cartSubtotal'));
    }

    public function process(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'shipping_method' => 'required|in:Pengiriman Standar,Pengiriman Ekspres',
            'payment_method' => 'required|in:midtrans',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $user = Auth::user();
            $cartItems = $user->cartItems()->with(['product', 'productVariant.size', 'productVariant.color'])->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Keranjang belanja Anda kosong.'], 400);
            }

            // Calculate order totals
            $cartSubtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $shippingCost = $request->shipping_method == 'Pengiriman Ekspres' ? 10000 : 0;
            $orderTotal = $cartSubtotal + $shippingCost;

            // Prepare Midtrans items
            $midtransItems = $cartItems->map(function ($item) {
                $productName = $item->product->name;
                $variantDetails = collect([
                    $item->productVariant->size->name ?? null,
                    $item->productVariant->color->name ?? null
                ])->filter()->implode(', ');

                if (!empty($variantDetails)) {
                    $productName .= " ($variantDetails)";
                }

                return [
                    'id' => $item->product_variant_id ?? $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => (int) $item->quantity,
                    'name' => $productName,
                ];
            })->toArray();

            if ($shippingCost > 0) {
                $midtransItems[] = [
                    'id' => 'SHIPPING_FEE',
                    'price' => (int) $shippingCost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman',
                ];
            }

            // Create or update order
            $order = $this->createOrUpdateOrder($user, $request, $orderTotal, $cartSubtotal, $shippingCost);

            // Generate Midtrans snap token
            $snapToken = $this->generateSnapToken($order, $request, $midtransItems);

            $order->midtrans_snap_token = $snapToken;
            $order->payment_redirect_url = $snapToken;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan.'
            ], 500);
        }
    }

    protected function createOrUpdateOrder($user, $request, $orderTotal, $cartSubtotal, $shippingCost)
    {
        // Cek apakah ada order yang belum dibayar untuk user ini
        $existingOrder = Order::where('user_id', $user->id)
            ->whereIn('payment_status', ['pending', 'unpaid'])
            ->where('created_at', '>', now()->subHours(2))
            ->first();

        $billingProvince = Province::where('name', $request->province)->first();
        $billingCity = City::where('name', $request->city)
                         ->where('province_id', $billingProvince->id ?? null)
                         ->first();

        if ($existingOrder) {
            // Update order yang ada
            $existingOrder->update([
                'total_amount' => $orderTotal,
                'subtotal_amount' => $cartSubtotal,
                'shipping_method' => $request->shipping_method,
                'shipping_cost' => $shippingCost,
                'payment_method' => $request->payment_method,
                'billing_first_name' => $request->first_name,
                'billing_last_name' => $request->last_name,
                'billing_email' => $request->email,
                'billing_phone' => $request->phone,
                'billing_address_line_1' => $request->address_line1,
                'billing_address_line_2' => $request->address_line2,
                'billing_province_id' => $billingProvince->id ?? null,
                'billing_city_id' => $billingCity->id ?? null,
                'billing_zip_code' => $request->zip_code,
                'shipping_first_name' => $request->first_name,
                'shipping_last_name' => $request->last_name,
                'shipping_email' => $request->email,
                'shipping_phone_number' => $request->phone,
                'shipping_address_line_1' => $request->address_line1,
                'shipping_address_line_2' => $request->address_line2,
                'shipping_province_id' => $billingProvince->id ?? null,
                'shipping_city_id' => $billingCity->id ?? null,
                'shipping_zip_code' => $request->zip_code,
            ]);

            // Hapus item order lama dan buat yang baru
            $existingOrder->orderItems()->delete();
            $this->createOrderItems($existingOrder, $user->cartItems);

            return $existingOrder;
        }

        // Jika tidak ada order yang belum dibayar, buat baru
        $orderNumber = 'ORD-' . date('YmdHis') . '-' . Str::random(6);

        $orderData = [
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'total_amount' => $orderTotal,
            'subtotal_amount' => $cartSubtotal,
            'discount_amount' => 0.00,
            'shipping_method' => $request->shipping_method,
            'shipping_cost' => $shippingCost,
            'payment_method' => $request->payment_method,
            'order_status' => 'pending',
            'payment_status' => 'pending',
            'billing_first_name' => $request->first_name,
            'billing_last_name' => $request->last_name,
            'billing_email' => $request->email,
            'billing_phone' => $request->phone,
            'billing_address_line_1' => $request->address_line1,
            'billing_address_line_2' => $request->address_line2,
            'billing_province_id' => $billingProvince->id ?? null,
            'billing_city_id' => $billingCity->id ?? null,
            'billing_zip_code' => $request->zip_code,
            'shipping_first_name' => $request->first_name,
            'shipping_last_name' => $request->last_name,
            'shipping_email' => $request->email,
            'shipping_phone_number' => $request->phone,
            'shipping_address_line_1' => $request->address_line1,
            'shipping_address_line_2' => $request->address_line2,
            'shipping_province_id' => $billingProvince->id ?? null,
            'shipping_city_id' => $billingCity->id ?? null,
            'shipping_zip_code' => $request->zip_code,
        ];

        $order = Order::create($orderData);
        $this->createOrderItems($order, $user->cartItems);

        return $order;
    }

    protected function createOrderItems($order, $cartItems)
    {
        $cartItems->each(function ($item) use ($order) {
            $order->orderItems()->create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product->name,
                'selected_size' => $item->productVariant->size->name ?? null,
                'selected_color' => $item->productVariant->color->name ?? null,
                'quantity' => $item->quantity,
                'price_per_item' => $item->price,
                'total_price' => $item->quantity * $item->price,
            ]);
        });
    }

    protected function generateSnapToken($order, $request, $midtransItems)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->billing_first_name,
                'last_name' => $order->billing_last_name,
                'email' => $order->billing_email,
                'phone' => $order->billing_phone,
                'billing_address' => [
                    'first_name' => $order->billing_first_name,
                    'last_name' => $order->billing_last_name,
                    'email' => $order->billing_email,
                    'phone' => $order->billing_phone,
                    'address' => $order->billing_address_line_1,
                    'city' => $request->city,
                    'postal_code' => $order->billing_zip_code,
                    'country_code' => 'IDN',
                ],
                'shipping_address' => [
                    'first_name' => $order->shipping_first_name,
                    'last_name' => $order->shipping_last_name,
                    'email' => $order->shipping_email,
                    'phone' => $order->shipping_phone_number,
                    'address' => $order->shipping_address_line_1,
                    'city' => $request->city,
                    'postal_code' => $order->shipping_zip_code,
                    'country_code' => 'IDN',
                ],
            ],
            'item_details' => $midtransItems,
            'callbacks' => [
                //'finish' => route('checkout.success', ['order_id' => $order->id]),
                'finish' => $this->midtransCallback($request),
                'error' => route('checkout.error', ['order_id' => $order->id]),
                'pending' => route('checkout.pending', ['order_id' => $order->id]),
            ],
        ];

        return Snap::getSnapToken($params);
    }

   public function midtransCallback(Request $request)
{
    $this->configureMidtrans();

    Log::error('Before execute midtrans callback');

    try {
        $notif = new Notification();

        // Debug log untuk melihat seluruh notifikasi
        Log::info('Midtrans Notification:', [
            'order_id' => $notif->order_id,
            'transaction_status' => $notif->transaction_status,
            'fraud_status' => $notif->fraud_status ?? null,
            'payment_type' => $notif->payment_type,
            'gross_amount' => $notif->gross_amount
        ]);

        $order = Order::where('order_number', $notif->order_id)->first();

        if (!$order) {
            Log::error('Order not found: ' . $notif->order_id);
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $this->processPaymentNotification($order, $notif);

        Log::info('Execute midtrans callback success');
        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        Log::error('Callback Error: ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}


    protected function processPaymentNotification($order, $notif)
{
    $transactionStatus = $notif->transaction_status;
    $fraudStatus = $notif->fraud_status ?? null;
    $paymentType = $notif->payment_type;

    // Log status saat ini sebelum diproses
    Log::info('Before Processing:', [
        'current_payment_status' => $order->payment_status,
        'current_order_status' => $order->order_status,
        'new_transaction_status' => $transactionStatus,
        'fraud_status' => $fraudStatus
    ]);

    // Mapping status
    $statusMap = [
        'capture' => [
            'challenge' => ['payment_status' => 'pending', 'order_status' => 'pending'],
            'accept' => ['payment_status' => 'paid', 'order_status' => 'processing']
        ],
        'settlement' => ['payment_status' => 'paid', 'order_status' => 'processing'],
        'pending' => ['payment_status' => 'pending', 'order_status' => 'pending'],
        'deny' => ['payment_status' => 'failed', 'order_status' => 'cancelled'],
        'expire' => ['payment_status' => 'expired', 'order_status' => 'cancelled'],
        'cancel' => ['payment_status' => 'cancelled', 'order_status' => 'cancelled']
    ];

    if ($transactionStatus == 'capture' && $fraudStatus && isset($statusMap['capture'][$fraudStatus])) {
        $order->fill($statusMap['capture'][$fraudStatus]);
    } elseif (isset($statusMap[$transactionStatus])) {
        $order->fill($statusMap[$transactionStatus]);
    }

    // Update informasi pembayaran
    $order->midtrans_transaction_id = $notif->transaction_id;
    $order->midtrans_payment_type = $paymentType;
    $order->midtrans_payment_time = $notif->settlement_time ?? now();
    $order->midtrans_gross_amount = $notif->gross_amount;

    // Jika menggunakan bank transfer
    if (isset($notif->va_numbers)) {
        $order->midtrans_bank = $notif->va_numbers[0]->bank ?? null;
        $order->midtrans_va_number = $notif->va_numbers[0]->va_number ?? null;
    }

    $order->save();

    // Log status setelah diproses
    Log::info('After Processing:', [
        'new_payment_status' => $order->payment_status,
        'new_order_status' => $order->order_status
    ]);

    // Jika pembayaran sukses, update stok dan kosongkan cart
    if ($order->payment_status === 'paid' && $order->order_status === 'processing') {
        try {
            DB::beginTransaction();

            $this->updateStock($order);
            Cart::where('user_id', $order->user_id)->delete();

            DB::commit();

            Log::info('Stock updated and cart cleared for order: ' . $order->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update stock: ' . $e->getMessage());
        }
    }
}

    protected function updateStock(Order $order)
    {
        DB::beginTransaction();
        try {
            foreach ($order->orderItems as $item) {
                $productVariant = ProductVariant::where('product_id', $item->product_id)
                    ->when($item->selected_size, function ($query, $size) {
                        $sizeId = Size::where('name', $size)->value('id');
                        $query->where('size_id', $sizeId);
                    })
                    ->when($item->selected_color, function ($query, $color) {
                        $colorId = Color::where('name', $color)->value('id');
                        $query->where('color_id', $colorId);
                    })
                    ->first();

                if ($productVariant) {
                    if ($productVariant->stock >= $item->quantity) {
                        $productVariant->decrement('stock', $item->quantity);
                        Log::info("Reduced stock for variant {$productVariant->id} by {$item->quantity}");
                    } else {
                        Log::error("Insufficient stock for variant {$productVariant->id}");
                        throw new \Exception("Insufficient stock for product variant");
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating stock: " . $e->getMessage());
            throw $e;
        }
    }

    public function success(Request $request)
    {
        $order = Order::find($request->query('order_id'));

        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('checkout.success', [
            'order' => $order->load('orderItems')
        ]);
    }

    public function pending(Request $request)
    {
        return view('checkout.pending', [
            'order' => Order::find($request->query('order_id'))
        ]);
    }

    public function error(Request $request)
    {
        return view('checkout.error', [
            'order' => Order::find($request->query('order_id'))
        ]);
    }

    public function listOrders()
    {
        return view('order', [
            'orders' => Auth::user()->orders()
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')->with('error', 'Akses ditolak.');
        }

        return view('orderdetail', [
            'order' => $order->load('orderItems')
        ]);
    }

    public function handlePopupClose(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::find($orderId);

        if ($order && $order->payment_status === 'pending') {
            $order->update([
                'payment_status' => 'unpaid',
                'order_status' => 'pending'
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
