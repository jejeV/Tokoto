<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CartController;
use App\Models\Order; // Asumsi Anda punya model Order
use App\Models\OrderDetail; // Asumsi Anda punya model OrderDetail
use App\Models\Product; // Untuk update stok
use App\Models\Province; // <--- Import model Province
use App\Models\City;     // <--- Import model City
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config; // Jika menggunakan Midtrans
use Midtrans\Snap; // Jika menggunakan Midtrans

class CheckoutController extends Controller
{
    protected $cartController;

    public function __construct(CartController $cartController)
    {
        $this->middleware('auth')->except(['handleCallback', 'checkoutSuccess', 'checkoutPending', 'checkoutError', 'getCitiesByProvince']);
        $this->cartController = $cartController;
    }

    /**
     * Menampilkan halaman checkout dengan data keranjang.
     * Rute: /checkout
     * Nama Rute: checkout.show
     */
    public function showCheckout()
    {
        $cartData = $this->cartController->getCartForCheckout();

        if ($cartData->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong. Silakan tambahkan produk sebelum checkout.');
        }

        $cartItems = $cartData->items;
        $cartSubtotal = $cartData->subtotal;

        // Ambil data user yang login (untuk pre-fill form)
        $user = Auth::user();

        // Ambil semua provinsi dari database
        $provinces = Province::all();

        // Siapkan data kota untuk pre-fill form billing/shipping jika ada old input atau user data
        $citiesForBillingForm = collect();
        $citiesForShippingForm = collect();

        // Ambil ID provinsi dari old input atau user data
        $billingProvinceId = old('billing_province_id', $user->province_id ?? null);
        if ($billingProvinceId) {
            $citiesForBillingForm = City::where('province_id', $billingProvinceId)->get();
        }

        // Jika form shipping tidak sama dengan billing dan ada old input/user data
        $sameAsBilling = old('same_as_billing', true); // Default true jika tidak ada old input
        if (!$sameAsBilling) {
            $shippingProvinceId = old('shipping_province_id', $user->province_id ?? null);
            if ($shippingProvinceId) {
                $citiesForShippingForm = City::where('province_id', $shippingProvinceId)->get();
            }
        }

        return view('checkout', compact('cartItems', 'cartSubtotal', 'user', 'provinces', 'citiesForBillingForm', 'citiesForShippingForm'));
    }

    /**
     * Memproses data checkout dan membuat order.
     * Rute: POST /checkout/process
     * Nama Rute: checkout.process
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'nullable|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone_number' => 'required|string|max:20',
            'billing_address_line_1' => 'required|string|max:255',
            'billing_province_id' => 'required|exists:provinces,id',
            'billing_city_id' => 'required|exists:cities,id',
            'billing_zip_code' => 'required|string|max:10',
            'shipping_method' => 'required|in:standard,express', // Validasi metode pengiriman
        ]);

        // Validasi untuk shipping jika 'same_as_billing' tidak dicentang
        if (!$request->input('same_as_billing')) {
            $request->validate([
                'shipping_first_name' => 'required|string|max:255',
                'shipping_last_name' => 'nullable|string|max:255',
                'shipping_email' => 'required|email|max:255',
                'shipping_phone_number' => 'required|string|max:20',
                'shipping_address_line_1' => 'required|string|max:255',
                'shipping_province_id' => 'required|exists:provinces,id',
                'shipping_city_id' => 'required|exists:cities,id',
                'shipping_zip_code' => 'required|string|max:10',
            ]);
        }

        $cartData = $this->cartController->getCartForCheckout();

        if ($cartData->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong. Tidak bisa melanjutkan checkout.');
        }

        DB::beginTransaction();
        try {
            // Hitung biaya pengiriman
            $shippingCost = 0;
            if ($request->input('shipping_method') === 'express') {
                $shippingCost = 10000;
            }

            $grossAmount = $cartData->subtotal + $shippingCost;

            // 1. Buat Order Baru
            $orderCode = 'INV-' . strtoupper(uniqid()); // Contoh kode order

            // Ambil data untuk customer_details & shipping/billing address
            $billingCityName = City::find($request->billing_city_id)->name;
            $billingProvinceName = Province::find($request->billing_province_id)->name;

            $customerDetails = [
                'first_name' => $request->billing_first_name,
                'last_name' => $request->billing_last_name ?? '',
                'email' => $request->billing_email,
                'phone' => $request->billing_phone_number,
                'billing_address' => [
                    'first_name' => $request->billing_first_name,
                    'last_name' => $request->billing_last_name ?? '',
                    'email' => $request->billing_email,
                    'phone' => $request->billing_phone_number,
                    'address' => $request->billing_address_line_1 . ($request->billing_address_line_2 ? ', ' . $request->billing_address_line_2 : ''),
                    'city' => $billingCityName,
                    'postal_code' => $request->billing_zip_code,
                    'country_code' => 'IDN',
                ],
            ];

            if ($request->input('same_as_billing')) {
                $shippingAddress = $customerDetails['billing_address'];
                $shippingFirstName = $request->billing_first_name;
                $shippingLastName = $request->billing_last_name ?? '';
                $shippingEmail = $request->billing_email;
                $shippingPhone = $request->billing_phone_number;
            } else {
                $shippingCityName = City::find($request->shipping_city_id)->name;
                $shippingProvinceName = Province::find($request->shipping_province_id)->name;
                $shippingAddress = [
                    'first_name' => $request->shipping_first_name,
                    'last_name' => $request->shipping_last_name ?? '',
                    'email' => $request->shipping_email,
                    'phone' => $request->shipping_phone_number,
                    'address' => $request->shipping_address_line_1 . ($request->shipping_address_line_2 ? ', ' . $request->shipping_address_line_2 : ''),
                    'city' => $shippingCityName,
                    'postal_code' => $request->shipping_zip_code,
                    'country_code' => 'IDN',
                ];
                $shippingFirstName = $request->shipping_first_name;
                $shippingLastName = $request->shipping_last_name ?? '';
                $shippingEmail = $request->shipping_email;
                $shippingPhone = $request->shipping_phone_number;
            }

            // Simpan Order ke Database
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => $orderCode,
                'customer_name' => $customerDetails['first_name'] . ' ' . $customerDetails['last_name'],
                'customer_email' => $customerDetails['email'],
                'customer_phone' => $customerDetails['phone'],
                'billing_address' => json_encode($customerDetails['billing_address']), // Simpan sebagai JSON
                'shipping_address' => json_encode($shippingAddress), // Simpan sebagai JSON
                'total_amount' => $grossAmount,
                'shipping_cost' => $shippingCost,
                'status' => 'pending', // Status awal
                'payment_status' => 'pending', // Status pembayaran dari Midtrans
                // 'payment_method' => 'midtrans', // Atau dari request jika ada pilihan lain
            ]);

            // 2. Tambahkan Detail Order dari Item Keranjang
            foreach ($cartData->items as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'], // Ini adalah product_id
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // Harga saat checkout
                    'selected_size' => $item['selected_size'],
                    'selected_color' => $item['selected_color'],
                ]);

                // 3. Kurangi Stok Produk
                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // 4. Kosongkan Keranjang Setelah Order Dibuat
            $this->cartController->clear(); // Panggil metode clear dari CartController

            DB::commit();

            // 5. Integrasi Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_code,
                    'gross_amount' => $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $shippingFirstName,
                    'last_name' => $shippingLastName,
                    'email' => $shippingEmail,
                    'phone' => $shippingPhone,
                    'billing_address' => $customerDetails['billing_address'],
                    'shipping_address' => $shippingAddress,
                ],
                'item_details' => $cartData->items->map(function($item) {
                    return [
                        'id' => $item['id'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'name' => $item['name'] .
                                  ($item['selected_size'] ? ' Size: ' . $item['selected_size'] : '') .
                                  ($item['selected_color'] ? ' Color: ' . $item['selected_color'] : ''),
                    ];
                })->toArray(),
            ];
            // Tambahkan biaya pengiriman sebagai item terpisah di Midtrans
            if ($shippingCost > 0) {
                $params['item_details'][] = [
                    'id' => 'SHIPPING_FEE',
                    'price' => $shippingCost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman',
                ];
            }


            try {
                $snapToken = Snap::getSnapToken($params);
                $order->snap_token = $snapToken;
                $order->save();

                // Redirect ke halaman yang akan memuat Midtrans Snap Pop-up
                return view('midtrans_payment', compact('snapToken', 'orderCode'));
            } catch (\Exception $e) {
                \Log::error("Midtrans Snap Token Error for Order " . $order->order_code . ": " . $e->getMessage());
                return redirect()->route('checkout.error')->with('error', 'Gagal mendapatkan token pembayaran. Silakan coba lagi.')->with('order_code', $order->order_code);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Process Error: ' . $e->getMessage());
            return redirect()->route('checkout.error')->with('error', 'Terjadi kesalahan saat memproses checkout Anda. Silakan coba lagi.');
        }
    }

    /**
     * Menangani callback dari Midtrans.
     * Rute: POST /payment/callback
     * Nama Rute: payment.callback
     * Tanpa Middleware CSRF
     */
    public function handleCallback(Request $request)
    {
        // Pastikan Anda sudah menginstal Midtrans SDK
        // composer require midtrans/midtrans-php
        // Dan konfigurasinya di config/services.php

        Config::$isProduction = config('services.midtrans.is_production');
        Config::$serverKey = config('services.midtrans.server_key');

        $notif = new \Midtrans\Notification(); // Inisialisasi Midtrans Notification

        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        $order = Order::where('order_code', $orderId)->first();

        if (!$order) {
            \Log::warning("Midtrans Callback: Order not found for ID {$orderId}");
            return response('Order not found', 404);
        }

        DB::beginTransaction();
        try {
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $order->status = 'success';
                }
            } else if ($transactionStatus == 'settlement') {
                $order->status = 'success';
            } else if ($transactionStatus == 'pending') {
                $order->status = 'pending';
            } else if ($transactionStatus == 'deny') {
                $order->status = 'failed';
            } else if ($transactionStatus == 'expire') {
                $order->status = 'expired';
            } else if ($transactionStatus == 'cancel') {
                $order->status = 'cancelled';
                // Jika order dibatalkan/kadaluarsa, kembalikan stok
                foreach ($order->orderDetails as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        $product->increment('stock', $detail->quantity);
                    }
                }
            }

            $order->payment_status = $transactionStatus; // Simpan status pembayaran dari Midtrans
            $order->save();

            DB::commit();
            return response('Callback processed successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Midtrans Callback Error for Order ' . $orderId . ': ' . $e->getMessage());
            return response('Error processing callback', 500);
        }
    }

    /**
     * Menampilkan daftar order user.
     * Rute: /orders
     * Nama Rute: orders.index
     */
    public function listOrders()
    {
        $orders = Order::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(10); // Contoh pagination

        return view('orders.index', compact('orders')); // Pastikan Anda memiliki view orders/index.blade.php
    }

    /**
     * Menampilkan detail order tertentu.
     * Rute: /orders/{orderCode}
     * Nama Rute: order.detail
     */
    public function showOrder($orderCode)
    {
        $order = Order::where('user_id', Auth::id())
                      ->where('order_code', $orderCode)
                      ->with('orderDetails.product') // Load detail order dan produknya
                      ->firstOrFail(); // Akan 404 jika tidak ditemukan

        return view('orders.detail', compact('order')); // Pastikan Anda memiliki view orders/detail.blade.php
    }

    /**
     * Menampilkan halaman sukses checkout.
     * Rute: /checkout/success
     * Nama Rute: checkout.success
     */
    public function checkoutSuccess(Request $request)
    {
        $orderCode = $request->query('order_id'); // Midtrans mengirim 'order_id' bukan 'order_code'
        $transactionStatus = $request->query('transaction_status');

        $order = null;
        if ($orderCode) {
            $order = Order::where('order_code', $orderCode)->first(); // Tidak perlu user_id di sini
        }
        return view('checkout_status.success', compact('order', 'transactionStatus'));
    }

    /**
     * Menampilkan halaman pending checkout.
     * Rute: /checkout/pending
     * Nama Rute: checkout.pending
     */
    public function checkoutPending(Request $request)
    {
        $orderCode = $request->query('order_id'); // Midtrans mengirim 'order_id' bukan 'order_code'
        $transactionStatus = $request->query('transaction_status');

        $order = null;
        if ($orderCode) {
            $order = Order::where('order_code', $orderCode)->first(); // Tidak perlu user_id di sini
        }
        return view('checkout_status.pending', compact('order', 'transactionStatus'));
    }

    /**
     * Menampilkan halaman error checkout.
     * Rute: /checkout/error
     * Nama Rute: checkout.error
     */
    public function checkoutError(Request $request)
    {
        $orderCode = $request->query('order_id'); // Midtrans mengirim 'order_id' bukan 'order_code'
        $transactionStatus = $request->query('transaction_status');

        $order = null;
        if ($orderCode) {
            $order = Order::where('order_code', $orderCode)->first(); // Tidak perlu user_id di sini
        }
        return view('checkout_status.error', compact('order', 'transactionStatus'));
    }

    /**
     * API untuk mendapatkan kota berdasarkan ID provinsi.
     * Rute: /api/cities/{provinceId}
     * Nama Rute: api.cities
     */
    public function getCitiesByProvince(Request $request, $provinceId)
    {
        // Logika untuk mengambil kota dari database
        // Pastikan Anda memiliki model City dan data di tabel 'cities'
        $cities = City::where('province_id', $provinceId)->get(['id', 'name']);

        return response()->json($cities);
    }
}
