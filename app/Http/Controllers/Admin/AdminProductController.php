<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['productVariants.size', 'productVariants.color'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        $totalProducts = Product::count();
        $inStockProducts = Product::whereHas('productVariants', function($query) {
            $query->where('stock', '>', 0);
        })->count();
        $outOfStockProducts = $totalProducts - $inStockProducts;
        $totalVariants = ProductVariant::count();

        $sizes = Size::all();
        $colors = Color::all();

        return view('partials.admin.products', compact(
            'products',
            'totalProducts',
            'inStockProducts',
            'outOfStockProducts',
            'totalVariants',
            'sizes',
            'colors'
        ));
    }

    public function create()
    {
        $sizes = Size::all();
        $colors = Color::all();

        return view('partials.admin.products.create', [
            'sizes' => $sizes,
            'colors' => $colors
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants' => 'nullable|array',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $productData = $request->only(['name', 'description', 'price']);

            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($productData);

            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variant) {
                    if (!empty($variant['size_id']) || !empty($variant['color_id']) || !empty($variant['stock'])) {
                        $product->productVariants()->create([
                            'size_id' => $variant['size_id'] ?? null,
                            'color_id' => $variant['color_id'] ?? null,
                            'stock' => $variant['stock'] ?? 0,
                            'price' => $variant['price'] ?? $product->price,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully!',
                    'redirect' => route('admin.products.index')
                ]);
            }

            return redirect()->route('admin.products.index')
                           ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation error: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating product: '.$e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->with('error', 'Error creating product: '.$e->getMessage())
                           ->withInput();
        }
    }

    public function show(Product $product)
    {
        return $this->prepareProductData($product);
    }

    public function edit(Product $product)
    {
        if (request()->ajax()) {
            return $this->prepareProductData($product);
        }

        $product->load('productVariants.size', 'productVariants.color');
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();

        return view('partials.admin.products.edit', compact('product', 'sizes', 'colors'));
    }

    private function prepareProductData(Product $product)
    {
        $product->load(['productVariants.size', 'productVariants.color']);
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image,
                'image_url' => $product->image ? Storage::url($product->image) : null,
                'variants' => $product->productVariants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'size_id' => $variant->size_id,
                        'color_id' => $variant->color_id,
                        'price' => $variant->price,
                        'stock' => $variant->stock,
                        'size_name' => $variant->size ? $variant->size->name : null,
                        'color_name' => $variant->color ? $variant->color->name : null,
                    ];
                })->toArray()
            ],
            'sizes' => $sizes,
            'colors' => $colors
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.size_id' => 'nullable|exists:sizes,id',
            'variants.*.color_id' => 'nullable|exists:colors,id',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'deleted_variants' => 'nullable|array',
            'deleted_variants.*' => 'exists:product_variants,id'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        DB::beginTransaction();
        try {
            $productData = $request->only(['name', 'description', 'price']);

            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $productData['image'] = $request->file('image')->store('products', 'public');
            } elseif ($request->has('remove_image') && $request->remove_image == '1') {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $productData['image'] = null;
            }

            $product->update($productData);

            $existingVariantIds = $product->productVariants->pluck('id')->toArray();
            $updatedVariantIds = [];

            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    if (empty($variantData['size_id']) && empty($variantData['color_id']) && empty($variantData['stock'])) {
                        continue;
                    }

                    $variantData['price'] = $variantData['price'] ?? $product->price;
                    $variantData['stock'] = $variantData['stock'] ?? 0;

                    if (!empty($variantData['id'])) {
                        $variant = $product->productVariants()->find($variantData['id']);
                        if ($variant) {
                            $variant->update([
                                'size_id' => $variantData['size_id'] ?? null,
                                'color_id' => $variantData['color_id'] ?? null,
                                'price' => $variantData['price'],
                                'stock' => $variantData['stock'],
                            ]);
                            $updatedVariantIds[] = $variant->id;
                        }
                    } else {
                        $variant = $product->productVariants()->create([
                            'size_id' => $variantData['size_id'] ?? null,
                            'color_id' => $variantData['color_id'] ?? null,
                            'price' => $variantData['price'],
                            'stock' => $variantData['stock'],
                        ]);
                        $updatedVariantIds[] = $variant->id;
                    }
                }
            }

            $idsToDelete = array_diff($existingVariantIds, $updatedVariantIds);
            if (!empty($idsToDelete)) {
                $product->productVariants()->whereIn('id', $idsToDelete)->delete();
            }

            if ($request->has('deleted_variants')) {
                $product->productVariants()->whereIn('id', $request->deleted_variants)->delete();
            }

            DB::commit();

            $product->refresh()->load(['productVariants.size', 'productVariants.color']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully!',
                    'product' => $product,
                    'redirect' => route('admin.products.index')
                ]);
            }

            return redirect()->route('admin.products.index')
                           ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update error: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: '.$e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->with('error', 'Failed to update product: '.$e->getMessage())
                           ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->productVariants()->delete();
            $product->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully!'
                ]);
            }

            return redirect()->route('admin.products.index')
                           ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion error: '.$e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete product: '.$e->getMessage()
                ], 500);
            }

            return redirect()->back()
                           ->with('error', 'Failed to delete product: '.$e->getMessage());
        }
    }

    public function updateStock(Request $request, $id, $type)
    {
        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $item = $type === 'product'
                ? Product::findOrFail($id)
                : ProductVariant::findOrFail($id);

            $item->update(['stock' => $request->stock]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock updated successfully!'
                ]);
            }

            return back()->with('success', 'Stock updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update stock: '.$e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to update stock: '.$e->getMessage());
        }
    }

    public function getFormData()
    {
        $sizes = Size::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'sizes' => $sizes,
            'colors' => $colors
        ]);
    }
}
