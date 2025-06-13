<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
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
    private const MAX_CART_ITEMS = 50;
    private const MAX_QUANTITY_PER_ITEM = 99;

    /**
     * Helper privat untuk mendapatkan pengenal keranjang (user_id atau session_id).
     * Ini menentukan set catatan keranjang mana yang akan berinteraksi.
     */
    private function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id()];
        }

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
        // Mendapatkan varian jika ada, atau produk utama jika tidak bervarian
        $productOrVariant = $guestItem->product_variant_id
            ? ProductVariant::find($guestItem->product_variant_id)
            : Product::find($guestItem->product_id);

        if (!$productOrVariant) {
            Log::warning("Product/Variant not found during merge (product_id: {$guestItem->product_id}, variant_id: {$guestItem->product_variant_id})", [
                'cart_item_id' => $guestItem->id,
            ]);
            $guestItem->delete(); // Hapus item jika produk/varian tidak valid
            return;
        }

        // Cek stok berdasarkan apakah ini varian atau produk utama
        $availableStock = $productOrVariant instanceof ProductVariant ? $productOrVariant->stock : $productOrVariant->stock;
        $currentPrice = $productOrVariant instanceof ProductVariant ? $productOrVariant->price : $productOrVariant->price;


        // Cari item yang cocok di keranjang user
        $userCartItem = Cart::where('user_id', $userId)
            ->where('product_id', $guestItem->product_id) // Tambahkan product_id untuk keakuratan
            ->where('product_variant_id', $guestItem->product_variant_id) // Akan null jika non-varian
            ->first();

        if ($userCartItem) {
            $newQuantity = $userCartItem->quantity + $guestItem->quantity;
            if ($newQuantity <= $availableStock && $newQuantity <= self::MAX_QUANTITY_PER_ITEM) {
                $userCartItem->quantity = $newQuantity;
                $userCartItem->save();
            } else {
                Log::warning("Guest cart merge: Quantity exceeds stock or max quantity for existing item (Cart ID: {$userCartItem->id})", [
                    'requested_quantity' => $newQuantity,
                    'available_stock' => $availableStock,
                    'max_quantity_per_item' => self::MAX_QUANTITY_PER_ITEM,
                ]);
            }
            $guestItem->delete(); // Hapus item tamu setelah digabungkan (atau gagal digabungkan karena batasan)
        } else {
            // Jika tidak ada item yang cocok di keranjang user, update item tamu menjadi milik user
            if ($guestItem->quantity <= $availableStock && $guestItem->quantity <= self::MAX_QUANTITY_PER_ITEM) {
                $guestItem->update([
                    'user_id' => $userId,
                    'session_id' => null, // Hapus session_id setelah dikaitkan dengan user
                    'price' => $currentPrice, // Update harga sesuai varian/produk saat ini
                ]);
            } else {
                Log::warning("Guest cart merge: Quantity exceeds stock or max quantity for new item (Cart ID: {$guestItem->id})", [
                    'requested_quantity' => $guestItem->quantity,
                    'available_stock' => $availableStock,
                    'max_quantity_per_item' => self::MAX_QUANTITY_PER_ITEM,
                ]);
                $guestItem->delete(); // Hapus item tamu jika kuantitasnya melebihi stok
            }
        }
    }


    /**
     * Menemukan varian produk berdasarkan produk dan atribut (ukuran/warna).
     * Jika sizeId dan colorId keduanya null, itu berarti mencari produk non-varian.
     */
    private function findProductVariant(int $productId, ?int $sizeId, ?int $colorId): ?ProductVariant
    {
        // Jika kedua ID null, artinya ini adalah produk yang tidak bervarian sama sekali.
        // Dalam kasus ini, kita tidak mencari di ProductVariant, tetapi di Product itu sendiri.
        // Namun, metode ini spesifik untuk ProductVariant.
        // Logic untuk produk non-varian akan ditangani langsung di method `add()` dan `validateCart()`.

        $query = ProductVariant::where('product_id', $productId);

        if ($sizeId !== null) {
            $query->where('size_id', $sizeId);
        } else {
            $query->whereNull('size_id');
        }

        if ($colorId !== null) {
            $query->where('color_id', $colorId);
        } else {
            $query->whereNull('color_id');
        }

        return $query->first();
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

        // Mengambil ukuran dan warna yang tersedia (ini bisa jadi daftar semua ukuran/warna yang aktif di sistem,
        // atau yang relevan dengan produk di keranjang). Untuk kesederhanaan, ambil semua yang aktif.
        $availableSizes = Size::active()->orderBy('sort_order')->get();
        $availableColors = Color::active()->orderBy('sort_order')->get();

        return view('cart', compact('cartItems', 'cartTotal', 'availableSizes', 'availableColors'));
    }

    /**
     * Menambahkan produk/varian ke keranjang.
     */
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'selected_size_id' => 'nullable|exists:sizes,id',
            'selected_color_id' => 'nullable|exists:colors,id',
            'quantity' => 'required|integer|min:1|max:' . self::MAX_QUANTITY_PER_ITEM,
        ]);

        $productId = $validated['product_id'];
        $selectedSizeId = $validated['selected_size_id'];
        $selectedColorId = $validated['selected_color_id'];
        $requestedQuantity = $validated['quantity'];

        DB::beginTransaction();
        try {
            $product = Product::find($productId);
            if (!$product) { // Should not happen due to exists:products,id validation
                return redirect()->back()->with('error', 'Produk tidak ditemukan.');
            }

            $productOrVariant = null;
            $stockAvailable = 0;
            $itemPrice = 0;
            $isVariant = false; // Flag untuk menentukan apakah ini varian atau produk utama

            // Cek apakah produk ini memiliki varian
            $hasVariants = $product->productVariants()->exists();

            if ($hasVariants) {
                // Jika produk memiliki varian, cari varian yang spesifik
                $productOrVariant = $this->findProductVariant(
                    $productId,
                    $selectedSizeId,
                    $selectedColorId
                );

                if (!$productOrVariant) {
                    return redirect()->back()->with('error', 'Kombinasi ukuran/warna tidak valid untuk produk ini.');
                }
                $stockAvailable = $productOrVariant->stock;
                $itemPrice = $productOrVariant->price;
                $isVariant = true;
            } else {
                // Produk tidak memiliki varian, gunakan data dari model Product langsung
                if ($selectedSizeId !== null || $selectedColorId !== null) {
                    // Ini adalah error: pengguna mencoba memilih varian pada produk non-varian
                    return redirect()->back()->with('error', 'Produk ini tidak memiliki varian ukuran atau warna.');
                }
                $productOrVariant = $product;
                $stockAvailable = $product->stock;
                $itemPrice = $product->price;
                $isVariant = false;
            }

            // Periksa stok produk/varian yang ditemukan
            if ($stockAvailable < $requestedQuantity) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $stockAvailable);
            }

            $identifier = $this->getCartIdentifier();

            // Cari item keranjang yang cocok (berdasarkan product_id DAN product_variant_id jika ada)
            $cartItemQuery = $this->getCartQuery()
                ->where('product_id', $productId);

            if ($isVariant) {
                $cartItemQuery->where('product_variant_id', $productOrVariant->id);
            } else {
                $cartItemQuery->whereNull('product_variant_id'); // Untuk produk non-varian
            }
            $cartItem = $cartItemQuery->first();


            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $requestedQuantity;

                if ($newQuantity > $stockAvailable) {
                    return redirect()->back()->with('error', 'Total kuantitas akan melebihi stok. Stok tersedia: ' . $stockAvailable);
                }

                if ($newQuantity > self::MAX_QUANTITY_PER_ITEM) {
                    return redirect()->back()->with('error', 'Maksimal ' . self::MAX_QUANTITY_PER_ITEM . ' item per produk.');
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Periksa kapasitas keranjang total (jumlah item berbeda) sebelum menambah item baru
                $currentCartItemsCount = $this->getCartQuery()->count();
                if ($currentCartItemsCount >= self::MAX_CART_ITEMS) {
                    return redirect()->back()->with('error', 'Keranjang penuh. Maksimal ' . self::MAX_CART_ITEMS . ' jenis item berbeda.');
                }

                Cart::create([
                    'user_id' => $identifier['user_id'] ?? null,
                    'session_id' => $identifier['session_id'] ?? null,
                    'product_id' => $productId,
                    'product_variant_id' => $isVariant ? $productOrVariant->id : null, // Set product_variant_id jika ini varian
                    'quantity' => $requestedQuantity,
                    'price' => $itemPrice, // Gunakan harga yang benar (dari produk atau varian)
                ]);
            }

            DB::commit();

            // Bangun pesan sukses dengan nama produk dan varian (jika ada)
            $productNameForMessage = $product->name . ($isVariant ? $this->buildVariantName($productOrVariant) : '');
            return redirect()->back()->with('success', 'Produk "' . $productNameForMessage . '" berhasil ditambahkan ke keranjang!');

        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();
            return redirect()->back()->with('error', 'Validasi gagal: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding to cart: ' . $e->getMessage(), [
                'product_id' => $productId,
                'selected_size_id' => $selectedSizeId,
                'selected_color_id' => $selectedColorId,
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(), // Log full trace for deeper debugging
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan internal saat menambahkan produk ke keranjang. Silakan coba lagi. Debug: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     */
    public function updateQuantity(Request $request, $cartItemId): RedirectResponse
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:' . self::MAX_QUANTITY_PER_ITEM,
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return redirect()->back()->with('error', 'Validasi kuantitas gagal: ' . implode(', ', $errors));
        }

        $newQuantity = $request->input('quantity');

        DB::beginTransaction();
        try {
            $cartItem = $this->getCartQuery()->find($cartItemId);

            if (!$cartItem) {
                return redirect()->route('cart.index')->with('error', 'Item keranjang tidak ditemukan.');
            }

            // Dapatkan stok dari varian atau produk utama
            $productOrVariant = $cartItem->productVariant
                ? $cartItem->productVariant
                : ($cartItem->product ?? null); // Fallback ke product jika tidak ada varian

            if (!$productOrVariant) {
                $cartItem->delete(); // Hapus item jika produk/varian tidak valid lagi
                Log::warning("Cart item removed due to missing product/variant: ID " . $cartItemId);
                return redirect()->route('cart.index')->with('error', 'Produk atau varian tidak ditemukan, item dihapus dari keranjang.');
            }
            $availableStock = $productOrVariant->stock;


            if ($newQuantity > $availableStock) {
                return redirect()->back()->with('error', 'Kuantitas melebihi stok yang tersedia: ' . $availableStock . '.');
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();

            DB::commit();
            return redirect()->route('cart.index')->with('success', 'Kuantitas berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cart item quantity: ' . $e->getMessage(), [
                'cart_item_id' => $cartItemId,
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui kuantitas.');
        }
    }

    /**
     * Memperbarui varian (ukuran/warna) item di keranjang.
     */
    public function updateVariant(Request $request, $cartItemId): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'size_id' => 'nullable|exists:sizes,id',
                'color_id' => 'nullable|exists:colors,id',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return redirect()->back()->with('error', 'Validasi varian gagal: ' . implode(', ', $errors));
        }

        $newSizeId = $validated['size_id'] ?? null;
        $newColorId = $validated['color_id'] ?? null;

        DB::beginTransaction();
        try {
            $cartItem = $this->getCartQuery()->find($cartItemId);

            if (!$cartItem) {
                return redirect()->route('cart.index')->with('error', 'Item keranjang tidak ditemukan.');
            }

            // Dapatkan produk terkait (penting untuk menemukan varian baru)
            $product = $cartItem->product;
            if (!$product) {
                 $cartItem->delete(); // Hapus item jika produk tidak valid
                Log::warning("Cart item removed due to missing product during variant update: ID " . $cartItemId);
                return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan, item dihapus dari keranjang.');
            }

            // Cari varian baru berdasarkan product_id asli dan size/color baru
            $targetVariant = $this->findProductVariant(
                $product->id,
                $newSizeId,
                $newColorId
            );

            if (!$targetVariant) {
                return redirect()->back()->with('error', 'Kombinasi ukuran/warna yang dipilih tidak tersedia untuk produk ini.');
            }

            // Jika varian yang dipilih sama dengan varian saat ini
            if ($targetVariant->id === $cartItem->product_variant_id) {
                DB::commit(); // Tidak ada perubahan, commit transaksi
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
            ->where('product_id', $product->id) // Tambahkan product_id untuk presisi
            ->where('product_variant_id', $targetVariant->id)
            ->where('id', '!=', $cartItem->id) // Jangan bandingkan dengan item yang sedang diupdate
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

            // Periksa stok untuk kuantitas item yang ada saat ini sebelum mengupdate varian
            if ($cartItem->quantity > $targetVariant->stock) {
                return redirect()->back()->with('error', 'Varian yang dipilih tidak memiliki stok yang cukup untuk kuantitas Anda saat ini. Stok tersedia: ' . $targetVariant->stock);
            }

            // Update item keranjang dengan varian baru
            $cartItem->product_variant_id = $targetVariant->id;
            $cartItem->price = $targetVariant->price; // Update harga juga sesuai varian baru
            $cartItem->save();

            DB::commit();
            return redirect()->route('cart.index')->with('success', 'Varian item keranjang berhasil diperbarui!');

        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();
            return redirect()->back()->with('error', 'Validasi varian gagal: ' . implode(', ', $errors));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cart item variant: ' . $e->getMessage(), [
                'cart_item_id' => $cartItemId,
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
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
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
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

            Log::info('Cart cleared', ['items_deleted' => $deletedCount, 'user_id' => Auth::id() ?? Session::get('cart_session_id')]);

            return back()->with('success', 'Keranjang belanja berhasil dikosongkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing cart: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
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
            Log::error('Error getting cart count: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['count' => 0], 500); // Mengembalikan 500 jika ada error
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
                'cartItems' => $cartItems, // Mengembalikan Koleksi model Cart
                'cartTotal' => $cartTotal
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching cart for checkout: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return [
                'cartItems' => collect(),
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
            $cartModified = false; // Flag untuk menandai jika ada perubahan di keranjang

            foreach ($cartItems as $item) {
                $productName = $item->product->name ?? 'Produk Tidak Dikenal';

                // Case 1: Varian produk tidak ditemukan atau sudah dihapus
                if ($item->product_variant_id && (!$item->productVariant || !$item->productVariant->exists)) {
                    $errors[] = "Produk '" . $productName . "' dengan varian ID {$item->product_variant_id} tidak tersedia lagi atau variannya hilang. Item telah dihapus.";
                    $item->delete(); // Hapus item dari cart jika variannya tidak valid
                    $cartModified = true;
                    continue; // Lanjutkan ke item berikutnya
                }
                // Case 2: Produk non-varian tidak ditemukan
                if (!$item->product_variant_id && (!$item->product || !$item->product->exists)) {
                    $errors[] = "Produk '" . $productName . "' tidak tersedia lagi atau telah dihapus. Item telah dihapus.";
                    $item->delete();
                    $cartModified = true;
                    continue;
                }

                $sourceEntity = $item->productVariant ?? $item->product; // Gunakan varian jika ada, jika tidak, gunakan produk
                $variantName = $this->buildVariantName($item->productVariant); // Hanya membangun nama jika ada varian

                // Case 3: Stok tidak mencukupi
                if ($item->quantity > $sourceEntity->stock) {
                    $errors[] = "Stok '" . $productName . $variantName . "' tidak mencukupi. Tersedia: {$sourceEntity->stock}. Jumlah di keranjang Anda: {$item->quantity}.";
                }

                // Case 4: Harga telah berubah
                if ($item->price != $sourceEntity->price) {
                    $errors[] = "Harga '" . $productName . $variantName . "' telah berubah dari Rp" . number_format($item->price, 0, ',', '.') . " menjadi Rp" . number_format($sourceEntity->price, 0, ',', '.') . ". Harga telah diperbarui.";
                    $item->price = $sourceEntity->price;
                    $item->save();
                    $cartModified = true;
                }
            }

            if ($cartModified) {
                $updatedCartData = $this->getCartForCheckout();
                return response()->json([
                    'valid' => empty($errors),
                    'errors' => $errors,
                    'cart_subtotal' => $updatedCartData['cartTotal'],
                    'cart_total_quantity' => $updatedCartData['cartItems']->sum('quantity'),
                    'cart_modified' => true // Memberi tahu frontend bahwa keranjang telah diubah
                ]);
            } else {
                // Jika tidak ada perubahan, kita bisa langsung hitung total dari $cartItems yang ada
                $currentCartTotal = $cartItems->sum(function($item) {
                    return $item->quantity * $item->price;
                });
                $currentCartTotalQuantity = $cartItems->sum('quantity');

                return response()->json([
                    'valid' => empty($errors),
                    'errors' => $errors,
                    'cart_subtotal' => $currentCartTotal,
                    'cart_total_quantity' => $currentCartTotalQuantity,
                    'cart_modified' => false
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error validating cart: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile(), [
                'user_id' => Auth::id(),
                'exception_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'valid' => false,
                'errors' => ['Terjadi kesalahan saat memvalidasi keranjang. Silakan coba lagi.'],
                'cart_subtotal' => 0,
                'cart_total_quantity' => 0,
                'cart_modified' => false
            ], 500);
        }
    }
}
