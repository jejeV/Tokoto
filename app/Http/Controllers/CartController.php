<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Pastikan Anda mengimpor model Product
use Illuminate\Support\Facades\Session; // Pastikan Anda mengimpor facade Session

class CartController extends Controller
{
    /**
     * Menampilkan isi keranjang belanja.
     */
    public function index()
    {
        $cartItems = Session::get('cart', []); // Ambil item dari session, defaultnya array kosong

        $cartTotal = $this->calculateCartTotal($cartItems);

        return view('cart', compact('cartItems', 'cartTotal'));
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1', // Quantity opsional, default 1
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1); // Default quantity 1

        $product = Product::find($productId);

        if (!$product) {
            return back()->with('error_add_to_cart', ['message' => 'Produk tidak ditemukan.', 'product_name' => 'Unknown Product']);
        }

        // Ambil keranjang saat ini dari session
        $cartItems = Session::get('cart', []);

        // Cek apakah produk sudah ada di keranjang
        if (isset($cartItems[$productId])) {
            // Tambahkan kuantitas yang ada
            $cartItems[$productId]['quantity'] += $quantity;
        } else {
            // Tambahkan produk baru ke keranjang
            $cartItems[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->image, // Sesuaikan jika nama kolom gambar berbeda
                'stock' => $product->stock, // Tambahkan stok untuk pengecekan nanti jika diperlukan
            ];
        }

        // Periksa stok (opsional, bisa juga dilakukan di middleware/request validation)
        if ($cartItems[$productId]['quantity'] > ($product->stock ?? 0)) {
            // Rollback kuantitas atau berikan pesan error
            $cartItems[$productId]['quantity'] -= $quantity; // Kurangi kembali kuantitas yang baru ditambahkan
            Session::put('cart', $cartItems); // Simpan kembali keranjang yang sudah di-rollback
            return back()->with('error_add_to_cart', [
                'message' => 'Jumlah produk melebihi stok yang tersedia.',
                'product_name' => $product->name,
                'cart_count' => count($cartItems)
            ]);
        }


        // Simpan kembali keranjang ke session
        Session::put('cart', $cartItems);

        return back()->with('success_add_to_cart', [
            'message' => 'Produk berhasil ditambahkan!',
            'product_name' => $product->name,
            'product_image' => $product->image,
            'cart_count' => count($cartItems)
        ]);
    }

    /**
     * Mengupdate kuantitas produk di keranjang.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->input('id');
        $quantity = $request->input('quantity');

        $cartItems = Session::get('cart', []);

        if (isset($cartItems[$productId])) {
            $product = Product::find($productId);

            if ($product && $quantity > ($product->stock ?? 0)) {
                return back()->with('error', 'Kuantitas melebihi stok yang tersedia untuk ' . $product->name . '. Stok saat ini: ' . ($product->stock ?? 0));
            }

            $cartItems[$productId]['quantity'] = $quantity;
            Session::put('cart', $cartItems);
            return back()->with('success', 'Kuantitas keranjang berhasil diperbarui.');
        }

        return back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required', // Hanya ID produk yang diperlukan
        ]);

        $productId = $request->input('id');
        $cartItems = Session::get('cart', []);

        // Hapus item dari array berdasarkan product ID
        if (isset($cartItems[$productId])) {
            unset($cartItems[$productId]);
            Session::put('cart', $cartItems);
            return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
        }

        return back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Mengosongkan seluruh isi keranjang.
     */
    public function clear()
    {
        Session::forget('cart'); // Hapus seluruh array 'cart' dari session
        return back()->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }

    /**
     * Helper: Menghitung total harga keranjang.
     */
    private function calculateCartTotal(array $cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += ($item['price'] * $item['quantity']);
        }
        return $total;
    }
}
