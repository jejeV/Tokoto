<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products (collections page).
     * Method untuk menampilkan semua produk.
     *
     * @return \Illuminate\View\View
     */
    public function showCollections()
    {
        // Hapus kondisi where('is_active', true)
        $products = Product::paginate(12);
        // Anda mungkin ingin filter atau order by di sini
        return view('collections', compact('products'));
    }

    /**
     * Display the specified product detail (shop-product/{id}).
     * Method untuk menampilkan detail produk tunggal.
     *
     * @param  int  $id  The ID of the product.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showProductDetail($id)
    {
        $product = Product::with([
            'productVariants.size',
            'productVariants.color'
        ])->findOrFail($id);

        // Jika produk tidak memiliki varian sama sekali, arahkan ke 404
        // Atau Anda bisa putuskan untuk tetap menampilkan produk tanpa varian
        // Jika Anda ingin tetap menampilkan produk tanpa varian, hapus if ini
        if ($product->productVariants->isEmpty() && $product->stock === 0) { // Tambahkan cek stock jika produk tidak punya varian
             return redirect()->route('404');
        }

        // Filter varian yang memiliki size_id dan color_id non-null atau null yang sesuai
        // Untuk memastikan varian yang di-grouping adalah yang valid untuk ditampilkan.
        // Asumsikan "One Size" diwakili oleh size_id = NULL
        // Asumsikan "No Color" diwakili oleh color_id = NULL
        $variantsBySize = $product->productVariants
                                  ->groupBy(function($item) {
                                      // Jika size_id null, gunakan string 'One Size' untuk grouping
                                      return $item->size->name ?? 'One Size';
                                  });

        $variantsByColor = $product->productVariants
                                   ->groupBy(function($item) {
                                       // Jika color_id null, gunakan string 'No Color' untuk grouping
                                       return $item->color->name ?? 'No Color';
                                   });

        // Ambil semua ukuran unik yang terkait dengan varian produk ini
        // Ini akan digunakan untuk JavaScript agar bisa memfilter pilihan
        $allSizesForProduct = $product->productVariants->pluck('size')->filter()->unique('id');

        // Ambil semua warna unik yang terkait dengan varian produk ini
        // Ini akan digunakan untuk JavaScript agar bisa memfilter pilihan
        $allColorsForProduct = $product->productVariants->pluck('color')->filter()->unique('id');

        // Tentukan default variant untuk ditampilkan.
        // Prefer varian pertama yang memiliki stok > 0.
        // Jika tidak ada varian dengan stok, ambil varian pertama secara acak.
        $defaultVariant = $product->productVariants->first(function ($variant) {
            return $variant->stock > 0;
        });

        // Jika tidak ada varian dengan stok, ambil varian pertama apa adanya
        if (!$defaultVariant) {
            $defaultVariant = $product->productVariants->first();
        }

        // Jika produk tidak memiliki varian sama sekali (misalnya hanya produk tunggal tanpa size/color),
        // maka kita perlu membuat objek "defaultVariant" dummy agar blade tidak error
        // dan bisa mengambil harga/stok dari objek produk itu sendiri.
        if (!$defaultVariant) { // Masih null setelah mencoba mencari dari productVariants
            // Jika product memiliki stock > 0, buat dummy variant
            if ($product->stock > 0) {
                $defaultVariant = (object) [
                    'id' => null, // Tidak ada ID varian spesifik
                    'product_id' => $product->id,
                    'size_id' => null,
                    'color_id' => null,
                    'stock' => $product->stock,
                    'price' => $product->price,
                    'image' => $product->image,
                    'size' => (object)['id' => null, 'name' => 'One Size'], // Dummy size object
                    'color' => (object)['id' => null, 'name' => 'No Color'], // Dummy color object
                ];
            } else {
                // Jika produk tidak memiliki varian dan juga tidak memiliki stok
                // Ini akan menyebabkan masalah jika tidak ada varian dan stok 0
                // Anda mungkin perlu mengarahkan ke 404 atau menampilkan pesan stok habis
                return redirect()->route('404'); // Atau handle sesuai kebijakan bisnis Anda
            }
        }


        // allColors diperlukan untuk mendapatkan hex_code warna di Blade
        // karena $variantsByColor hanya memiliki nama warna sebagai key.
        $allColors = Color::all(); // Ini adalah semua warna yang ada di DB, bukan hanya yang terkait dengan produk

        return view('product', compact(
            'product',
            'defaultVariant',
            'variantsBySize',
            'variantsByColor',
            'allColors', // Digunakan untuk mendapatkan hex_code warna
            'allSizesForProduct', // Lewatkan ini ke view untuk JS
            'allColorsForProduct' // Lewatkan ini ke view untuk JS
        ));
    }
}
