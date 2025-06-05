<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk (halaman koleksi) dari database.
     * Route: GET /collections (contoh)
     */
    public function showCollections()
    {
        try {
            $products = Product::all();
        } catch (\Exception $e) {
            $products = [];
            Log::error('Gagal mengambil data produk: ' . $e->getMessage());
            return view('collections', ['products' => [], 'error' => 'Gagal memuat produk. Silakan coba lagi nanti.']);
        }
        return view('collections', compact('products'));
    }

    /**
     * Menampilkan detail satu produk dari database.
     * Route: GET /shop-product/{id} (contoh)
     *
     * @param int
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showProductDetail($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('collections')->with('error', 'Produk tidak ditemukan.');
        }

        return view('product', compact('product'));
    }
}
