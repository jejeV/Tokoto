<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Order; 
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function __construct()
    {
        // Pastikan pengguna sudah login sebelum bisa mengakses halaman checkout
        $this->middleware('auth');
    }

    /**
     * Menampilkan formulir informasi pengiriman (Langkah 1 Checkout).
     *
     * @return View|RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        // Pastikan keranjang tidak kosong
        if (Cart::total() <= 0) {
            Session::flash('error', 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.');
            return redirect()->route('products.index'); // Kembali ke halaman produk
        }

        // Anda bisa mengisi form dengan data pengguna yang sudah ada jika login
        $user = Auth::user();
        return view('checkout.index', compact('user'));
    }

    /**
     * Memproses informasi pengiriman dan menampilkan ringkasan pesanan (Langkah 2 Checkout).
     * Ini bisa digabung ke 'index' jika alurnya sederhana.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function review(Request $request): View|RedirectResponse
    {
        // Validasi data pengiriman
        $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            // Tambahkan validasi lain sesuai kebutuhan (nama, email, dll.)
        ]);

        // Simpan informasi pengiriman ke sesi sementara
        Session::put('checkout.shipping_info', $request->only(['address', 'city', 'province', 'postal_code', 'phone']));

        $cartItems = Cart::content();
        $subtotal = Cart::subtotal();
        $tax = Cart::tax();
        $total = Cart::total();
        $shippingInfo = Session::get('checkout.shipping_info');

        // Di sini Anda bisa menghitung biaya pengiriman jika ada
        // $shippingCost = 0;
        // Session::put('checkout.shipping_cost', $shippingCost);

        return view('checkout.review', compact('cartItems', 'subtotal', 'tax', 'total', 'shippingInfo'));
    }

    /**
     * Memproses pesanan (Langkah terakhir Checkout).
     * Ini akan menyimpan pesanan ke database dan mengosongkan keranjang.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function process(Request $request): RedirectResponse
    {
        // Pastikan keranjang tidak kosong dan info pengiriman ada di sesi
        if (Cart::total() <= 0 || !Session::has('checkout.shipping_info')) {
            Session::flash('error', 'Terjadi kesalahan pada proses checkout. Silakan ulangi.');
            return redirect()->route('cart.index');
        }

        // Lakukan proses pembayaran di sini (integrasi payment gateway)
        // Jika pembayaran berhasil, lanjutkan ke proses penyimpanan pesanan

        DB::beginTransaction();
        try {
            $shippingInfo = Session::get('checkout.shipping_info');
            $user = Auth::user();

            // Buat entri pesanan baru
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()), // Generate nomor pesanan unik
                'total_amount' => Cart::total(),
                'status' => 'pending', // Atau 'paid' jika pembayaran langsung dikonfirmasi
                'shipping_address' => $shippingInfo['address'],
                'shipping_city' => $shippingInfo['city'],
                'shipping_province' => $shippingInfo['province'],
                'shipping_postal_code' => $shippingInfo['postal_code'],
                'shipping_phone' => $shippingInfo['phone'],
                // Tambahkan kolom lain seperti shipping_cost, payment_method, dll.
            ]);

            // Tambahkan setiap item dari keranjang ke order_items
            foreach (Cart::content() as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => $item->qty,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ]);

                // Kurangi stok produk (penting!)
                // $product = Product::find($item->id);
                // if ($product) {
                //     $product->decrement('stock', $item->qty);
                // }
            }

            // Kosongkan keranjang setelah pesanan berhasil
            Cart::destroy();
            Session::forget('checkout.shipping_info'); // Hapus info pengiriman dari sesi

            DB::commit();

            Session::flash('success', 'Pesanan Anda berhasil dibuat!');
            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi. Error: ' . $e->getMessage());
            return redirect()->route('checkout.index'); // Kembali ke halaman checkout
        }
    }

    /**
     * Menampilkan halaman sukses setelah pesanan dibuat.
     *
     * @param int $orderId
     * @return View|RedirectResponse
     */
    public function success(int $orderId): View|RedirectResponse
    {
        try {
            $order = Order::with('orderItems.product')->findOrFail($orderId);
            if ($order->user_id !== Auth::id()) { // Pastikan pengguna hanya bisa melihat pesanannya sendiri
                abort(403);
            }
        } catch (ModelNotFoundException $e) {
            Session::flash('error', 'Pesanan tidak ditemukan.');
            return redirect()->route('home');
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Menampilkan halaman gagal jika proses pembayaran gagal.
     * (Implementasi lebih lanjut jika menggunakan payment gateway)
     *
     * @return View
     */
    public function failed(): View
    {
        return view('checkout.failed');
    }
}