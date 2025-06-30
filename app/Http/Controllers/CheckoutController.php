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
// use Midtrans\Notification; // Dihapus sesuai permintaan
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    /**
     * Mengkonfigurasi kunci Midtrans dan mode produksi.
     */
    protected function configureMidtrans()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Menampilkan formulir checkout dengan item keranjang belanja pengguna.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Memproses pesanan dan menghasilkan Snap Token Midtrans.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            'shipping_method' => ['required', Rule::in(['Pengiriman Standar', 'Pengiriman Ekspres'])],
            'payment_method' => ['required', Rule::in(['midtrans'])],
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

            // Hitung total pesanan
            $cartSubtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $shippingCost = $request->shipping_method == 'Pengiriman Ekspres' ? 10000 : 0;
            $orderTotal = $cartSubtotal + $shippingCost;

            // Siapkan item untuk Midtrans
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

            $order = $this->createOrUpdateOrder($user, $request, $orderTotal, $cartSubtotal, $shippingCost);

            $snapToken = $this->generateSnapToken($order, $request, $midtransItems);

            $order->midtrans_snap_token = $snapToken;
            $order->payment_redirect_url = null; // URL redirect Midtrans tidak lagi digunakan, ditangani oleh JS
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order->id // Kirim ID order internal Anda ke frontend
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat atau memperbarui pesanan di database.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @param float $orderTotal
     * @param float $cartSubtotal
     * @param float $shippingCost
     * @return \App\Models\Order
     */
    protected function createOrUpdateOrder($user, $request, $orderTotal, $cartSubtotal, $shippingCost)
    {
        $existingOrder = Order::where('user_id', $user->id)
            ->whereIn('payment_status', ['pending', 'unpaid'])
            ->where('created_at', '>', now()->subHours(2)) // Pertimbangkan durasi pending yang wajar
            ->first();

        $billingProvince = Province::where('name', $request->province)->first();
        $billingCity = City::where('name', $request->city)
            ->when($billingProvince, function($query) use ($billingProvince) {
                return $query->where('province_id', $billingProvince->id);
            })
            ->first();

        if ($existingOrder) {
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
                'payment_status' => 'pending', // Status awal selalu pending
                'order_status' => 'pending',   // Status awal selalu pending
            ]);

            $existingOrder->orderItems()->delete(); // Hapus item lama jika memperbarui pesanan
            $this->createOrderItems($existingOrder, $user->cartItems);

            Log::info("Existing order {$existingOrder->id} updated for user {$user->id}.");
            return $existingOrder;
        }

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

        Log::info("New order {$order->id} created for user {$user->id}.");
        return $order;
    }

    /**
     * Membuat item pesanan dari item keranjang belanja.
     *
     * @param \App\Models\Order $order
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     */
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

    /**
     * Menghasilkan Snap Token Midtrans.
     * Menghilangkan bagian 'callbacks' karena callback akan ditangani di sisi JS.
     *
     * @param \App\Models\Order $order
     * @param \Illuminate\Http\Request $request
     * @param array $midtransItems
     * @return string Snap Token
     */
    protected function generateSnapToken($order, $request, $midtransItems)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number, // Order ID yang unik untuk Midtrans
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
            // Array 'callbacks' Dihapus karena kita akan menangani callback melalui JavaScript Snap di frontend
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Memproses notifikasi pembayaran dan memperbarui status pesanan.
     * Ini adalah fungsi pembantu yang digunakan oleh handleMidtransSuccess, handleMidtransPending, dll.
     *
     * @param \App\Models\Order $order
     * @param object $notif Objek yang meniru notifikasi Midtrans (dari webhook atau JS callback)
     */
    protected function processPaymentNotification($order, $notif)
    {
        $transactionStatus = $notif->transaction_status;
        // Gunakan $notif->fraud_status hanya jika ada di objek $notif yang diterima
        // Karena permintaan untuk menyederhanakan, kita asumsikan fraud_status tidak selalu ada
        $fraudStatus = property_exists($notif, 'fraud_status') ? $notif->fraud_status : null;
        $paymentType = $notif->payment_type;

        Log::info('Before Processing (Internal):', [
            'order_id' => $order->id,
            'current_payment_status' => $order->payment_status,
            'current_order_status' => $order->order_status,
            'new_transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus
        ]);

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

        $order->midtrans_transaction_id = $notif->transaction_id ?? null;
        $order->midtrans_payment_type = $paymentType;
        $order->midtrans_gross_amount = $notif->gross_amount;
        $order->midtrans_status_code = $notif->status_code ?? null; // Pastikan ini ada di DB dan model

        // Hapus penugasan untuk kolom yang tidak ada di DB (sesuai permintaan penyederhanaan migrasi)
        // Jika kolom ini tidak ada di tabel `orders`, baris berikut tidak boleh dieksekusi
        // atau akan menyebabkan error "Unknown column".
        // Karena sudah dihapus dari migrasi, maka kita hapus juga dari controller.
        // $order->midtrans_bank = null;
        // $order->midtrans_va_number = null;
        // $order->midtrans_va_numbers = null;
        // $order->midtrans_bill_key = null;
        // $order->midtrans_biller_code = null;
        // $order->midtrans_masked_card = null; // Jika ada di DB asli tapi dihapus dari model/migrasi
        // $order->midtrans_settlement_time = null; // Jika ada di DB asli tapi dihapus dari model/migrasi
        // $order->midtrans_fraud_status = null; // Jika ada di DB asli tapi dihapus dari model/migrasi

        $order->save();

        Log::info('After Processing (Internal):', [
            'order_id' => $order->id,
            'new_payment_status' => $order->payment_status,
            'new_order_status' => $order->order_status
        ]);

        // Perbarui stok dan kosongkan keranjang hanya jika status pembayaran menjadi 'paid' dan stok belum diperbarui
        if ($order->payment_status === 'paid' && $order->order_status === 'processing' && !$order->stock_updated_at) {
            try {
                DB::beginTransaction();
                $this->updateStock($order);
                Cart::where('user_id', $order->user_id)->delete();
                $order->update(['stock_updated_at' => now()]);
                DB::commit();
                Log::info('Stock updated and cart cleared for order (from processPaymentNotification): ' . $order->id);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to update stock and clear cart (from processPaymentNotification): ' . $e->getMessage());
            }
        }
    }

    /**
     * Memperbarui stok produk berdasarkan item pesanan.
     *
     * @param \App\Models\Order $order
     * @throws \Exception
     */
    protected function updateStock(Order $order)
    {
        Log::info("Attempting to update stock for order: " . $order->id);
        foreach ($order->orderItems as $item) {
            $productVariant = ProductVariant::where('product_id', $item->product_id)
                ->when($item->selected_size, function ($query, $sizeName) {
                    $size = Size::where('name', $sizeName)->first();
                    if ($size) {
                        return $query->where('size_id', $size->id);
                    }
                    return $query->whereNull('size_id');
                })
                ->when($item->selected_color, function ($query, $colorName) {
                    $color = Color::where('name', $colorName)->first();
                    if ($color) {
                        return $query->where('color_id', $color->id);
                    }
                    return $query->whereNull('color_id');
                })
                ->first();

            if ($productVariant) {
                if ($productVariant->stock >= $item->quantity) {
                    $productVariant->decrement('stock', $item->quantity);
                    Log::info("Reduced stock for variant {$productVariant->id} by {$item->quantity}. New stock: {$productVariant->stock}");
                } else {
                    Log::error("Insufficient stock for variant {$productVariant->id} for order {$order->id}. Required: {$item->quantity}, Available: {$productVariant->stock}");
                    throw new \Exception("Insufficient stock for product variant ID: {$productVariant->id}");
                }
            } else {
                Log::error("Product variant not found for order item ID: {$item->id}, Product ID: {$item->product_id}, Size: {$item->selected_size}, Color: {$item->selected_color}");
                throw new \Exception("Product variant not found for order item.");
            }
        }
        Log::info("Stock update completed for order: " . $order->id);
    }

    /**
     * Menangani callback sukses Midtrans Snap dari frontend JavaScript.
     * Fungsi ini akan dipanggil melalui AJAX dari sisi klien setelah pembayaran berhasil.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleMidtransSuccess(Request $request)
    {
        Log::info('handleMidtransSuccess received. Data:', $request->all());

        // Validasi data yang masuk dari callback Snap JS, disederhanakan
        $validator = \Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id', // Pastikan ini adalah ID order dari DB Anda
            'transaction_status' => 'required|string',
            'transaction_id' => 'required|string',
            'gross_amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'status_code' => 'required|string', // Kode status dari Midtrans
            // Field-field lain seperti fraud_status, va_numbers, permata_va_number, bill_key, biller_code dihapus dari validasi
        ]);

        if ($validator->fails()) {
            Log::error('Midtrans success callback validation failed (simplified):', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $validator->errors()], 400);
        }

        // Temukan pesanan menggunakan ID order internal Anda
        $order = Order::find($request->order_id);

        if (!$order) {
            Log::error('Order not found in handleMidtransSuccess for ID: ' . $request->order_id);
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        // Cek jika status pesanan sudah 'paid' atau 'processing' untuk mencegah update duplikat
        if ($order->payment_status === 'paid' && $order->order_status === 'processing') {
            Log::info("Order {$order->id} already paid. Skipping update in handleMidtransSuccess.");
            return response()->json(['success' => true, 'message' => 'Order already processed.']);
        }

        try {
            DB::beginTransaction();

            $this->processPaymentNotification($order, (object)[
                'transaction_status' => $request->transaction_status,
                'transaction_id' => $request->transaction_id,
                'gross_amount' => $request->gross_amount,
                'payment_type' => $request->payment_type,
                'status_code' => $request->status_code,
            ]);

            DB::commit();
            Log::info("Order {$order->id} status updated successfully from JS success callback (simplified).");

            // Kembalikan respons sukses. Frontend akan menangani redirect.
            return response()->json(['success' => true, 'message' => 'Order status updated.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status from JS success callback for order ' . $order->id . ': ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to update order status.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menangani callback pending Midtrans Snap dari frontend JavaScript.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleMidtransPending(Request $request)
    {
        Log::info('handleMidtransPending received. Data:', $request->all());

        $validator = \Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'transaction_status' => 'required|string',
            'transaction_id' => 'required|string',
            'gross_amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'status_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Midtrans pending callback validation failed:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $validator->errors()], 400);
        }

        $order = Order::find($request->order_id);

        if (!$order) {
            Log::error('Order not found in handleMidtransPending for ID: ' . $request->order_id);
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        try {
            DB::beginTransaction();

            $this->processPaymentNotification($order, (object)[
                'transaction_status' => $request->transaction_status,
                'transaction_id' => $request->transaction_id,
                'gross_amount' => $request->gross_amount,
                'payment_type' => $request->payment_type,
                'status_code' => $request->status_code,
            ]);

            DB::commit();
            Log::info("Order {$order->id} status updated to pending successfully from JS pending callback.");
            return response()->json(['success' => true, 'message' => 'Order status updated.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status from JS pending callback for order ' . $order->id . ': ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to update order status.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menangani callback error Midtrans Snap dari frontend JavaScript.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleMidtransError(Request $request)
    {
        Log::info('handleMidtransError received. Data:', $request->all());

        $validator = \Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'transaction_status' => 'required|string',
            'transaction_id' => 'required|string',
            'gross_amount' => 'required|numeric',
            'payment_type' => 'required|string',
            'status_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Midtrans error callback validation failed:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Invalid data provided.', 'errors' => $validator->errors()], 400);
        }

        $order = Order::find($request->order_id);

        if (!$order) {
            Log::error('Order not found in handleMidtransError for ID: ' . $request->order_id);
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        try {
            DB::beginTransaction();

            $this->processPaymentNotification($order, (object)[
                'transaction_status' => $request->transaction_status,
                'transaction_id' => $request->transaction_id,
                'gross_amount' => $request->gross_amount,
                'payment_type' => $request->payment_type,
                'status_code' => $request->status_code,
            ]);

            DB::commit();
            Log::info("Order {$order->id} status updated to error/failed successfully from JS error callback.");
            return response()->json(['success' => true, 'message' => 'Order status updated.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order status from JS error callback for order ' . $order->id . ': ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Failed to update order status.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fungsi untuk halaman sukses yang akan di-redirect setelah pembayaran berhasil.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        $order = Order::find($request->query('order_id'));
        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan.');
        }
        // Pastikan orderItem dimuat jika dibutuhkan di view success
        return view('checkout.success', [
            'order' => $order->load('orderItems')
        ]);
    }

    /**
     * Fungsi untuk halaman pending pembayaran.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function pending(Request $request)
    {
        $order = Order::find($request->query('order_id'));
        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan.');
        }
        return view('checkout.pending', [
            'order' => $order
        ]);
    }

    /**
     * Fungsi untuk halaman error pembayaran.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function error(Request $request)
    {
        $order = Order::find($request->query('order_id'));
        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan.');
        }
        return view('checkout.error', [
            'order' => $order
        ]);
    }

    /**
     * Menampilkan daftar pesanan pengguna yang sedang login.
     *
     * @return \Illuminate\View\View
     */
    public function listOrders()
    {
        return view('order', [
            'orders' => Auth::user()->orders()
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }

    /**
     * Menampilkan detail pesanan tertentu.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')->with('error', 'Akses ditolak.');
        }

        return view('orderdetail', [
            'order' => $order->load('orderItems')
        ]);
    }

    /**
     * Menangani peristiwa penutupan popup Midtrans oleh pengguna.
     * Mengubah status pembayaran menjadi 'unpaid' jika sebelumnya 'pending'.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePopupClose(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::find($orderId);



        if ($order && $order->payment_status === 'pending') {
            $order->update([
                'payment_status' => 'unpaid',
                'order_status' => 'pending' // Atau 'cancelled' tergantung logika bisnis Anda
            ]);
            Log::info("Order {$orderId} status changed to unpaid due to Midtrans popup close.");
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Order not found or not in pending status.']);
    }
}
