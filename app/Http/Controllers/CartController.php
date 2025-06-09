<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart; // Pastikan ini ada dan mengarah ke model Cart Anda
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    private const MAX_CART_ITEMS = 50; // Batasan jumlah item unik di keranjang
    private const MAX_QUANTITY_PER_ITEM = 99; // Batasan kuantitas per item

    /**
     * Helper privat untuk mendapatkan pengenal keranjang (user_id atau session_id).
     * Ini menentukan set catatan keranjang mana yang akan berinteraksi.
     */
    private function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }

        // Jika user tidak login dan tidak ada session_id keranjang, buat yang baru
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Str::uuid()->toString());
        }

        return ['session_id' => Session::get('cart_session_id')];
    }

    /**
     * Mendapatkan query item keranjang dengan batasan cakupan yang tepat
     */
    private function getCartQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $identifier = $this->getCartIdentifier();

        return Cart::where(function($query) use ($identifier) {
            if (isset($identifier['user_id'])) {
                $query->where('user_id', $identifier['user_id']);
            } else {
                $query->where('session_id', $identifier['session_id']);
            }
        });
    }

    /**
     * Menghitung total kuantitas item dalam keranjang (jumlah dari semua kuantitas).
     */
    private function getCartItemsCount(): int
    {
        return $this->getCartQuery()->sum('quantity');
    }

    /**
     * Menggabungkan item keranjang tamu ke keranjang pengguna yang terautentikasi saat login.
     */
    public function mergeGuestCart(): void
    {
        if (!Auth::check() || !Session::has('cart_session_id')) {
            return;
        }

        $guestSessionId = Session::get('cart_session_id');
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $guestCartItems = Cart::where('session_id', $guestSessionId)->get();

            if ($guestCartItems->isEmpty()) {
                Session::forget('cart_session_id');
                DB::commit();
                return;
            }

            foreach ($guestCartItems as $guestItem) {
                $this->mergeGuestCartItem($guestItem, $userId);
            }

            Session::forget('cart_session_id');
            DB::commit();

            Log::info("Guest cart merged successfully for user: {$userId}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error merging guest cart: ' . $e->getMessage(), [
                'user_id' => $userId,
                'guest_session_id' => $guestSessionId
            ]);
        }
    }

    /**
     * Menggabungkan item keranjang tamu individu.
     */
    private function mergeGuestCartItem(Cart $guestItem, int $userId): void
    {
        $productVariant = ProductVariant::find($guestItem->product_variant_id);

        if (!$productVariant) {
            Log::warning("Variant not found during merge (product_variant_id: {$guestItem->product_variant_id})", [
                'cart_item_id' => $guestItem->id,
                'product_id' => $guestItem->product_id,
                'product_variant_id' => $guestItem->product_variant_id
            ]);
            $guestItem->delete(); // Hapus item jika varian tidak valid
            return;
        }

        $userCartItem = Cart::where('user_id', $userId)
            ->where('product_variant_id', $productVariant->id)
            ->first();

        if ($userCartItem) {
            $newQuantity = $userCartItem->quantity + $guestItem->quantity;
            if ($newQuantity <= $productVariant->stock && $newQuantity <= self::MAX_QUANTITY_PER_ITEM) {
                $userCartItem->quantity = $newQuantity;
                $userCartItem->save();
            }
            $guestItem->delete(); // Hapus item tamu setelah digabungkan
        } else {
            // Jika tidak ada item yang cocok di keranjang user, update item tamu menjadi milik user
            if ($guestItem->quantity <= $productVariant->stock && $guestItem->quantity <= self::MAX_QUANTITY_PER_ITEM) {
                $guestItem->update([
                    'user_id' => $userId,
                    'session_id' => null, // Hapus session_id setelah dikaitkan dengan user
                    'product_variant_id' => $productVariant->id, // Pastikan id varian benar
                    'price' => $productVariant->price, // Update harga sesuai varian
                ]);
            } else {
                $guestItem->delete(); // Hapus item tamu jika kuantitasnya melebihi stok
            }
        }
    }

    /**
     * Menemukan varian produk berdasarkan produk dan atribut (ukuran/warna).
     */
    private function findProductVariant(int $productId, ?int $sizeId, ?int $colorId): ?ProductVariant
    {
        return ProductVariant::where('product_id', $productId)
            ->where(function ($query) use ($sizeId) {
                if ($sizeId !== null) {
                    $query->where('size_id', $sizeId);
                } else {
                    $query->whereNull('size_id');
                }
            })
            ->where(function ($query) use ($colorId) {
                if ($colorId !== null) {
                    $query->where('color_id', $colorId);
                } else {
                    $query->whereNull('color_id');
                }
            })
            ->first();
    }

    /**
     * Menampilkan konten keranjang.
     */
    public function index(): View
    {
        // Mendapatkan data keranjang dalam format yang diinginkan Blade
        $cartData = $this->getCartDataForView();
        $cartItems = $cartData['cartItems']; // Ini adalah Collection of Cart Models
        $cartTotal = $cartData['cartTotal'];

        // --- DEBUG: Uncomment baris di bawah ini untuk melihat tipe data $cartItems ---
        // if ($cartItems->isNotEmpty()) {
        //     dd($cartItems->first()); // Akan menampilkan objek App\Models\Cart
        // } else {
        //     dd('Keranjang kosong di index method.');
        // }
        // --- End Debug ---

        // Mengambil ukuran dan warna yang tersedia
        $availableSizes = Size::active()->orderBy('sort_order')->get();
        $availableColors = Color::active()->orderBy('sort_order')->get();

        return view('cart', compact('cartItems', 'cartTotal', 'availableSizes', 'availableColors'));
    }

    /**
     * Menambahkan varian produk ke keranjang.
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'selected_size_id' => 'nullable|exists:sizes,id',
            'selected_color_id' => 'nullable|exists:colors,id',
            'quantity' => 'required|integer|min:1|max:' . self::MAX_QUANTITY_PER_ITEM,
        ]);

        DB::beginTransaction();
        try {
            $variant = $this->findProductVariant(
                $validated['product_id'],
                $validated['selected_size_id'],
                $validated['selected_color_id']
            );

            if (!$variant) {
                return redirect()->back()->with('error', 'Kombinasi ukuran/warna tidak valid untuk produk ini.');
            }

            // Periksa kapasitas keranjang total (jumlah item berbeda)
            $currentCartItemsCount = $this->getCartQuery()->count(); // Menghitung jumlah item unik
            if ($currentCartItemsCount >= self::MAX_CART_ITEMS &&
                !$this->getCartQuery()->where('product_variant_id', $variant->id)->exists()) {
                // Jika keranjang penuh dan item belum ada di keranjang
                return redirect()->back()->with('error', 'Keranjang penuh. Maksimal ' . self::MAX_CART_ITEMS . ' jenis item berbeda.');
            }

            if ($variant->stock < $validated['quantity']) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $variant->stock);
            }

            $identifier = $this->getCartIdentifier();
            $cartItem = $this->getCartQuery()
                ->where('product_variant_id', $variant->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $validated['quantity'];

                if ($newQuantity > $variant->stock) {
                    return redirect()->back()->with('error', 'Total kuantitas akan melebihi stok. Stok tersedia: ' . $variant->stock);
                }

                if ($newQuantity > self::MAX_QUANTITY_PER_ITEM) {
                    return redirect()->back()->with('error', 'Maksimal ' . self::MAX_QUANTITY_PER_ITEM . ' item per produk.');
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                Cart::create([
                    'user_id' => $identifier['user_id'] ?? null,
                    'session_id' => $identifier['session_id'] ?? null,
                    'product_id' => $validated['product_id'],
                    'product_variant_id' => $variant->id,
                    'quantity' => $validated['quantity'],
                    'price' => $variant->price, // Simpan harga varian saat ini
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding to cart: ' . $e->getMessage(), [
                'product_id' => $validated['product_id'],
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan produk ke keranjang.');
        }
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function updateQuantity(Request $request, $cartItemId): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . self::MAX_QUANTITY_PER_ITEM,
        ]);

        $newQuantity = $request->input('quantity');

        DB::beginTransaction();
        try {
            $cartItem = $this->getCartQuery()->find($cartItemId);

            if (!$cartItem) {
                return redirect()->route('cart.index')->with('error', 'Item keranjang tidak ditemukan.');
            }

            $productVariant = ProductVariant::find($cartItem->product_variant_id);

            if (!$productVariant) {
                $cartItem->delete(); // Hapus item jika varian tidak ada atau tidak aktif
                return redirect()->route('cart.index')->with('error', 'Varian produk tidak ditemukan atau tidak aktif, item dihapus.');
            }

            if ($newQuantity > $productVariant->stock) {
                return redirect()->back()->with('error', 'Kuantitas melebihi stok. Stok tersedia: ' . $productVariant->stock);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            DB::commit();
            return redirect()->route('cart.index')->with('success', 'Kuantitas berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cart item quantity: ' . $e->getMessage(), [
                'cart_item_id' => $cartItemId,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui kuantitas.');
        }
    }

    /**
     * Memperbarui varian (ukuran/warna) item di keranjang.
     */
    public function updateVariant(Request $request, $cartItemId): RedirectResponse
    {
        $validated = $request->validate([
            'size_id' => 'nullable|exists:sizes,id',
            'color_id' => 'nullable|exists:colors,id',
        ]);

        $newSizeId = $validated['size_id'] ?? null;
        $newColorId = $validated['color_id'] ?? null;

        DB::beginTransaction();
        try {
            $cartItem = $this->getCartQuery()->find($cartItemId);

            if (!$cartItem) {
                return redirect()->route('cart.index')->with('error', 'Item keranjang tidak ditemukan.');
            }

            // Dapatkan varian produk saat ini untuk mendapatkan product_id
            $currentProductVariant = ProductVariant::find($cartItem->product_variant_id);
            if (!$currentProductVariant) {
                $cartItem->delete(); // Hapus item jika varian saat ini tidak ditemukan
                return redirect()->route('cart.index')->with('error', 'Varian produk asli tidak ditemukan atau tidak aktif, item dihapus.');
            }

            // Cari varian baru berdasarkan product_id asli dan size/color baru
            $targetVariant = $this->findProductVariant(
                $currentProductVariant->product_id, // Gunakan product_id dari varian asli
                $newSizeId,
                $newColorId
            );

            if (!$targetVariant) {
                return redirect()->back()->with('error', 'Kombinasi ukuran/warna tidak tersedia untuk produk ini.');
            }

            // Jika varian yang dipilih sama dengan varian saat ini
            if ($targetVariant->id === $cartItem->product_variant_id) {
                return redirect()->route('cart.index')->with('success', 'Varian sudah terpilih.');
            }

            // Periksa apakah varian baru sudah ada di keranjang sebagai item terpisah
            $identifier = $this->getCartIdentifier();
            $existingItemWithNewVariant = Cart::where(function($query) use ($identifier) {
                if (isset($identifier['user_id'])) {
                    $query->where('user_id', $identifier['user_id']);
                } else {
                    $query->where('session_id', $identifier['session_id']);
                }
            })
            ->where('product_variant_id', $targetVariant->id)
            ->where('id', '!=', $cartItem->id) // Jangan bandingkan dengan item itu sendiri
            ->first();

            if ($existingItemWithNewVariant) {
                // Jika varian baru sudah ada, gabungkan kuantitas
                $mergedQuantity = $existingItemWithNewVariant->quantity + $cartItem->quantity;

                if ($mergedQuantity > $targetVariant->stock) {
                    return redirect()->back()->with('error', 'Total kuantitas setelah penggabungan akan melebihi stok tersedia: ' . $targetVariant->stock . '.');
                }
                if ($mergedQuantity > self::MAX_QUANTITY_PER_ITEM) {
                    return redirect()->back()->with('error', 'Maksimal ' . self::MAX_QUANTITY_PER_ITEM . ' item per produk setelah penggabungan.');
                }

                $existingItemWithNewVariant->quantity = $mergedQuantity;
                $existingItemWithNewVariant->save();
                $cartItem->delete(); // Hapus item lama

                DB::commit();
                return redirect()->route('cart.index')->with('success', 'Item keranjang berhasil digabungkan!');
            }

            // Periksa stok untuk kuantitas item yang ada saat ini
            if ($cartItem->quantity > $targetVariant->stock) {
                return redirect()->back()->with('error', 'Varian yang dipilih tidak memiliki stok yang cukup untuk kuantitas Anda saat ini. Stok tersedia: ' . $targetVariant->stock);
            }

            // Update item keranjang dengan varian baru
            $cartItem->product_variant_id = $targetVariant->id;
            $cartItem->price = $targetVariant->price; // Update harga juga sesuai varian baru
            $cartItem->save();

            DB::commit();
            return redirect()->route('cart.index')->with('success', 'Varian item keranjang berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cart item variant: ' . $e->getMessage(), [
                'cart_item_id' => $cartItemId,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui varian keranjang: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function remove(Request $request, $cartItemId): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $cartItem = $this->getCartQuery()->find($cartItemId);

            if (!$cartItem) {
                return redirect()->route('cart.index')->with('error', 'Item keranjang tidak ditemukan.');
            }

            $cartItem->delete();
            DB::commit();

            return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari keranjang!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing cart item: ' . $e->getMessage(), [
                'cart_item_id' => $cartItemId,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus produk.');
        }
    }

    /**
     * Mengosongkan semua item dari keranjang.
     */
    public function clear(): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $deletedCount = $this->getCartQuery()->delete();
            DB::commit();

            Log::info('Cart cleared', ['items_deleted' => $deletedCount]);

            return back()->with('success', 'Keranjang belanja berhasil dikosongkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing cart: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat mengosongkan keranjang.');
        }
    }

    /**
     * Mendapatkan jumlah item keranjang untuk badge/counter (biasanya via AJAX).
     */
    public function getCartCount(): JsonResponse
    {
        try {
            $count = $this->getCartItemsCount();
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting cart count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Mengambil item keranjang sebagai koleksi Eloquent dan menghitung totalnya.
     * Metode ini dirancang untuk dipanggil oleh controller lain (seperti CheckoutController)
     * dan menyediakan model Eloquent mentah sehingga properti dapat diakses.
     */
    public function getCartForCheckout(): array
    {
        try {
            $cartItems = $this->getCartQuery()
                ->with(['product', 'productVariant.size', 'productVariant.color'])
                ->get(); // Ini mengembalikan Koleksi model Cart

            $cartTotal = $cartItems->sum(function($item) {
                // $item di sini adalah model Cart, jadi $item->quantity dan $item->price benar.
                return $item->quantity * $item->price;
            });

            return [
                'cartItems' => $cartItems, // Mengembalikan koleksi Eloquent secara langsung
                'cartTotal' => $cartTotal
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching cart for checkout: ' . $e->getMessage());
            return [
                'cartItems' => collect(), // Mengembalikan koleksi kosong saat error
                'cartTotal' => 0,
                'error' => 'Gagal mengambil data keranjang. Silakan coba lagi nanti.'
            ];
        }
    }

    /**
     * Helper untuk mendapatkan data keranjang dalam format persis yang diharapkan oleh
     * panggilan `compact` metode `index()`. Metode ini juga mengembalikan koleksi
     * Eloquent mentah untuk cartItems.
     */
    private function getCartDataForView(): array
    {
        $cartItems = $this->getCartQuery()
            ->with(['product', 'productVariant.size', 'productVariant.color'])
            ->orderBy('created_at', 'desc')
            ->get(); // Ini mengembalikan Koleksi model Cart

        $cartTotal = $cartItems->sum(function($item) {
            // $item di sini adalah model Cart, jadi $item->quantity dan $item->price benar.
            return $item->quantity * $item->price;
        });

        return [
            'cartItems' => $cartItems, // Mengembalikan Koleksi model Cart
            'cartTotal' => $cartTotal
        ];
    }

    /**
     * Membangun string nama varian (misalnya: " (L - Merah)").
     */
    private function buildVariantName(?ProductVariant $variant): string
    {
        if (!$variant) {
            return '';
        }

        $parts = [];

        if ($variant->size && $variant->size->name) {
            $parts[] = $variant->size->name;
        }

        if ($variant->color && $variant->color->name) {
            $parts[] = $variant->color->name;
        }

        return empty($parts) ? '' : ' (' . implode(' - ', $parts) . ')';
    }

    /**
     * Memvalidasi keranjang sebelum checkout (misalnya via AJAX).
     * Metode ini dirancang untuk mengembalikan respons JSON untuk validasi sisi klien.
     */
    public function validateCart(): JsonResponse
    {
        try {
            $cartItems = $this->getCartQuery()
                ->with(['product', 'productVariant'])
                ->get();

            $errors = [];

            foreach ($cartItems as $item) {
                // $item di sini adalah model Cart
                if (!$item->productVariant) {
                    $errors[] = "Produk '" . ($item->product->name ?? 'Tidak Dikenal') . "' dengan varian ID {$item->product_variant_id} tidak tersedia lagi atau variannya hilang.";
                    // Hapus item dari cart jika variannya tidak valid
                    $item->delete();
                    continue;
                }

                if ($item->quantity > $item->productVariant->stock) {
                    $errors[] = "Stok '" . ($item->product->name ?? 'Produk Tidak Dikenal') . $this->buildVariantName($item->productVariant) . "' tidak mencukupi. Tersedia: {$item->productVariant->stock}. Jumlah di keranjang Anda: {$item->quantity}.";
                }

                if ($item->price != $item->productVariant->price) {
                    $errors[] = "Harga '" . ($item->product->name ?? 'Produk Tidak Dikenal') . $this->buildVariantName($item->productVariant) . "' telah berubah dari Rp" . number_format($item->price, 0, ',', '.') . " menjadi Rp" . number_format($item->productVariant->price, 0, ',', '.') . ".";
                    // Update harga di cart
                    $item->price = $item->productVariant->price;
                    $item->save();
                }
            }

            // Setelah validasi, jika ada perubahan (seperti update harga atau penghapusan),
            // kita harus mengambil kembali data keranjang terbaru untuk mendapatkan total terbaru.
            $updatedCartData = $this->getCartForCheckout(); // Ini sekarang mengembalikan model Eloquent dan total

            return response()->json([
                'valid' => empty($errors),
                'errors' => $errors,
                'cart_subtotal' => $updatedCartData['cartTotal'],
                'cart_total_quantity' => $updatedCartData['cartItems']->sum('quantity'), // Jumlah dari koleksi Eloquent
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating cart: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'errors' => ['Terjadi kesalahan saat memvalidasi keranjang.'],
                'cart_subtotal' => 0,
                'cart_total_quantity' => 0,
            ], 500);
        }
    }
}
