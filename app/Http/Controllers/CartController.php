<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session; // Untuk pesan flash

class CartController extends Controller
{
    /**
     * Menampilkan isi keranjang belanja.
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        // Mendapatkan semua item dalam keranjang
        $cartItems = Cart::content();
        return view('cart.index', compact('cartItems'));
    }

    /**
     * Menambahkan produk ke keranjang belanja.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product (Route model binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity');

        // Pastikan produk memiliki stok yang cukup (ini adalah logika tambahan)
        // if ($product->stock < $quantity) {
        //     Session::flash('error', 'Stok produk tidak mencukupi.');
        //     return Redirect::back();
        // }

        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'qty' => $quantity,
            'weight' => 0, // Anda bisa menambahkan kolom 'weight' di tabel produk jika perlu
            'options' => [
                'image' => $product->image, // Simpan gambar untuk ditampilkan di keranjang
                // Tambahkan opsi lain jika ada (misal: 'size', 'color')
            ],
        ]);

        Session::flash('success', 'Produk berhasil ditambahkan ke keranjang!');
        return Redirect::route('cart.index'); // Langsung ke halaman keranjang setelah menambah
    }

    /**
     * Memperbarui jumlah item di keranjang belanja.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $rowId (ID baris unik untuk item di keranjang)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $rowId): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        Cart::update($rowId, $request->input('quantity'));

        Session::flash('success', 'Jumlah produk di keranjang berhasil diperbarui!');
        return Redirect::route('cart.index');
    }

    /**
     * Menghapus item dari keranjang belanja.
     *
     * @param  string  $rowId (ID baris unik untuk item di keranjang)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(string $rowId): \Illuminate\Http\RedirectResponse
    {
        Cart::remove($rowId);

        Session::flash('info', 'Produk berhasil dihapus dari keranjang.');
        return Redirect::route('cart.index');
    }

    /**
     * Mengosongkan seluruh isi keranjang belanja.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(): \Illuminate\Http\RedirectResponse
    {
        Cart::destroy(); // Menghapus semua item di keranjang

        Session::flash('info', 'Keranjang belanja Anda telah dikosongkan.');
        return Redirect::route('cart.index');
    }
}