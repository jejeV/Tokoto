<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk atau produk berdasarkan kategori.
     *
     * @param Request $request
     * @return View|Response
     */
    public function index(Request $request): View|Response
    {
        $categorySlug = $request->input('category');

        $products = Product::query();

        if ($categorySlug) {
            try {
                $category = Category::where('slug', $categorySlug)->firstOrFail();
                $products->where('category_id', $category->id);
            } catch (ModelNotFoundException $e) {
                // Redirect atau tampilkan error jika kategori tidak ditemukan
                return redirect()->route('products.index')->withErrors(['category' => 'Kategori tidak ditemukan.']);
            }
        }

        // Urutkan produk, misalnya berdasarkan nama atau tanggal terbaru
        $products = $products->orderBy('created_at', 'desc')->paginate(12);
        $categories = Category::all();

        // Ini untuk penanganan AJAX, jika Anda ingin memuat produk secara dinamis
        if ($request->ajax()) {
            return response()->json([
                'products_html' => view('product.partials.product_list', compact('products'))->render(),
                'pagination_html' => $products->links()->toHtml()
            ]);
        }

        return view('product.index', compact('products', 'categories'));
    }

    /**
     * Menampilkan detail produk tunggal.
     *
     * @param int $id
     * @return View|Response
     */
    public function show(int $id): View|Response
    {
        try {
            $product = Product::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->view('errors.404', ['message' => 'Produk tidak ditemukan.'], 404);
        }

        // Anda bisa mengambil produk terkait atau kategori lainnya di sini jika diperlukan
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->inRandomOrder()
                                ->limit(4)
                                ->get();

        return view('product.show', compact('product', 'relatedProducts'));
    }
}