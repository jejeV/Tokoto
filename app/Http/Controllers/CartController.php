<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Pastikan Anda mengimpor model Product

class CartController extends Controller
{
    /**
     * Menampilkan daftar produk (halaman koleksi) dari database.
     */
    public function showCollections()
    {
        // Ambil data produk dari database.
        // Asumsikan Anda memiliki model Product dan ingin mengambil semua produk.
        // Anda dapat menyesuaikan query ini sesuai kebutuhan Anda (misalnya, pagination, filter, dll.).
        try {
            $products = Product::all(); // Mengambil semua produk dari tabel 'products'
        } catch (\Exception $e) {
            // Tangani kesalahan jika gagal mengambil data dari database
            // Misalnya, log kesalahan, tampilkan pesan error, dll.
            // Untuk contoh ini, kita akan mengembalikan array kosong.
            $products = [];
            // Log the error
            \Log::error('Gagal mengambil data produk: ' . $e->getMessage());
            // Optionally, you can return an error message to the view
            // return view('collections', ['products' => [], 'error' => 'Gagal mengambil data produk. Silakan coba lagi.']);
        }

        // Atau, jika Anda ingin menggunakan pagination:
        // $products = Product::paginate(9); // 9 produk per halaman

        return view('collections', compact('products'));
    }

    /**
     * Menambahkan produk ke keranjang belanja.
     * Dipanggil dari halaman koleksi produk.
     */
    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $productName = $request->input('product_name');
        $productPrice = $request->input('price');
        $productImage = $request->input('image');
        $productStock = $request->input('stock');

        $cart = session()->get('cart', []);

        // --- Validasi Stok (Penting) ---
        // Sebaiknya selalu ambil stok terbaru dari database untuk validasi yang akurat.
         $productFromDb = Product::find($productId);
         $actualStock = $productFromDb ? $productFromDb->stock : 0;
        // $actualStock = $productStock; // Jika tidak ada DB check, gunakan nilai dari form/sesi

        // Hitung total kuantitas semua item yang sudah ada di keranjang
        $totalQuantityInCartBeforeAction = 0;
        foreach ($cart as $item) {
            $totalQuantityInCartBeforeAction += $item['quantity'];
        }

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + 1;
            if ($newQuantity <= $actualStock) {
                $cart[$productId]['quantity'] = $newQuantity;
                session()->put('cart', $cart);

                $totalQuantityInCartAfterAction = $totalQuantityInCartBeforeAction + 1;

                return redirect()->back()->with('success_add_to_cart', [
                    'message' => 'Berhasil!',
                    'product_name' => $productName,
                    'product_image' => $productImage,
                    'cart_count' => $totalQuantityInCartAfterAction,
                ]);
            } else {
                return redirect()->back()->with('error_add_to_cart', [
                    'message' => 'Tidak dapat menambahkan lebih banyak. Stok maksimum untuk produk ini adalah ' . $actualStock . '.',
                    'product_name' => $productName,
                    'product_image' => $productImage,
                    'cart_count' => $totalQuantityInCartBeforeAction,
                ]);
            }
        } else {
            if ($actualStock > 0) {
                $cart[$productId] = [
                    "id" => $productId,
                    "name" => $productName,
                    "quantity" => 1,
                    "price" => $productPrice,
                    "image" => $productImage,
                    "stock" => $actualStock
                ];
                session()->put('cart', $cart);

                $totalQuantityInCartAfterAction = $totalQuantityInCartBeforeAction + 1;

                return redirect()->back()->with('success_add_to_cart', [
                    'message' => 'Berhasil!',
                    "product_name" => $productName,
                    "product_image" => $productImage,
                    'cart_count' => $totalQuantityInCartAfterAction,
                ]);
            } else {
                return redirect()->back()->with('error_add_to_cart', [
                    'message' => 'Produk tidak tersedia (stok kosong).',
                    "product_name" => $productName,
                    "product_image" => $productImage,
                    'cart_count' => $totalQuantityInCartBeforeAction,
                ]);
            }
        }
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        $grandTotal = 0;
        foreach ($cart as $item) {
            $grandTotal += $item['price'] * $item['quantity'];
        }
        return view('cart', compact('cart', 'grandTotal'));
    }


    public function updateCartQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $action = $request->input('action');

        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        $item = $cart[$productId];

         $productFromDb = Product::find($productId);
         $actualStock = $productFromDb ? $productFromDb->stock : $item['stock'];

        $message = '';

        if ($action === 'increase') {
            if ($item['quantity'] < $actualStock) {
                $cart[$productId]['quantity']++;
                $message = 'Kuantitas produk berhasil ditambah.';
            } else {
                $message = 'Tidak dapat menambah kuantitas. Stok maksimum (' . $actualStock . ') telah tercapai.';
                return redirect()->back()->with('error', $message);
            }
        } elseif ($action === 'decrease') {
            if ($item['quantity'] > 1) {
                $cart[$productId]['quantity']--;
                $message = 'Kuantitas produk berhasil dikurangi.';
            } else {
                unset($cart[$productId]);
                $message = 'Produk dihapus dari keranjang.';
            }
        } else {
            return redirect()->back()->with('error', 'Aksi tidak valid.');
        }

        session()->put('cart', $cart); // Simpan keranjang yang diperbarui

        // Hitung ulang total kuantitas semua item setelah perubahan
        $newCartTotalQuantity = 0;
        foreach ($cart as $item) {
            $newCartTotalQuantity += $item['quantity'];
        }

        return redirect()->back()->with('success', $message)->with('cart_updated', [
            'product_id' => $productId,
            'new_quantity' => $cart[$productId]['quantity'] ?? 0, // Kuantitas baru atau 0 jika dihapus
            'cart_count' => $newCartTotalQuantity,
        ]);
    }

    public function removeProduct(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
            $message = 'Produk berhasil dihapus dari keranjang.';

            $newCartTotalQuantity = 0;
            foreach ($cart as $item) {
                $newCartTotalQuantity += $item['quantity'];
            }

            return redirect()->back()->with('success', $message)->with('cart_updated', [
                'product_id' => $productId,
                'new_quantity' => 0,
                'cart_count' => $newCartTotalQuantity,
            ]);
        }
        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }
}
