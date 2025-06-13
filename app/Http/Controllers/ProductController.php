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
     * Method to display all products.
     *
     * @return \Illuminate->View->View
     */
    public function showCollections()
    {
        $products = Product::paginate(12);
        return view('collections', compact('products'));
    }

    /**
     * Display the specified product detail (shop-product/{id}).
     * Method to display single product detail.
     *
     * @param  int  $id  The ID of the product.
     * @return \Illuminate->View->View|\Illuminate->Http->RedirectResponse
     */
    public function showProductDetail($id)
    {
        $product = Product::with([
            'productVariants.size',
            'productVariants.color'
        ])->findOrFail($id);

        // Fetch all unique sizes associated with this product's variants
        // Ordered by name for consistent display (e.g., S, M, L or 39, 40, 41)
        $allSizes = Size::whereHas('productVariants', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->orderBy('name')->get();

        // Fetch all unique colors associated with this product's variants
        // Ordered by name for consistent display
        $allColors = Color::whereHas('productVariants', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->orderBy('name')->get();

        // Prepare a map to easily check variant stock by size_id and color_id
        // Using 'null_size' or 'null_color' as keys for variants without a specific size/color
        $variantMap = [];
        foreach ($product->productVariants as $variant) {
            $sizeId = $variant->size_id ?? 'null_size'; // Key for size
            $colorId = $variant->color_id ?? 'null_color'; // Key for color

            if (!isset($variantMap[$sizeId])) {
                $variantMap[$sizeId] = [];
            }
            $variantMap[$sizeId][$colorId] = [
                'id' => $variant->id,
                'stock' => $variant->stock,
                'price' => $variant->price,
                'image' => $variant->image,
                // Add any other properties from productVariant that might be needed in JS
            ];
        }

        // Determine the default variant to display on page load
        // Prioritize a variant with stock > 0
        $defaultVariant = $product->productVariants->first(function ($variant) {
            return $variant->stock > 0;
        });

        // If no variant has stock, take the first available variant (regardless of stock)
        if (!$defaultVariant) {
            $defaultVariant = $product->productVariants->first();
        }

        // Handle case where product has no variants but has stock directly on the product model
        if (!$defaultVariant && $product->stock > 0 && $allSizes->isEmpty() && $allColors->isEmpty()) {
            $defaultVariant = (object) [
                'id' => null, // Indicates this is the main product, not a variant
                'product_id' => $product->id,
                'size_id' => null,
                'color_id' => null,
                'stock' => $product->stock,
                'price' => $product->price,
                'image' => $product->image,
                'size' => (object)['id' => null, 'name' => 'One Size'], // Placeholder for single products
                'color' => (object)['id' => null, 'name' => 'No Color'], // Placeholder for single products
            ];
        }

        // If product has no variants AND no stock directly on the product, redirect to 404
        if (!$defaultVariant && $product->stock === 0 && $product->productVariants->isEmpty()) {
            return redirect()->route('404');
        }

        return view('product', compact(
            'product',
            'defaultVariant',
            'allSizes',       // This is the variable that needs to be passed
            'allColors',      // This is the variable that needs to be passed
            'variantMap'      // This is the variable that needs to be passed
        ));
    }
}
