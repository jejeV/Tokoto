<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminProductController;

/*-------------------------------------------------------------------------
| Public Routes
|------------------------------------------------------------------------*/

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/collections', [ProductController::class, 'showCollections'])->name('collections');
Route::get('/shop-product/{id}', [ProductController::class, 'showProductDetail'])->name('shop.product.detail');

/*-------------------------------------------------------------------------
| Cart Routes
|------------------------------------------------------------------------*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'add'])->name('add');
    Route::put('{cartItem}/update-quantity', [CartController::class, 'updateQuantity'])->name('update_quantity');
    Route::put('{cartItem}/update-variant', [CartController::class, 'updateVariant'])->name('update_variant');
    Route::delete('{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::post('clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
    Route::post('/validate', [CartController::class, 'validateCart'])->name('validate');
});

/*-------------------------------------------------------------------------
| Authentication Routes
|------------------------------------------------------------------------*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('login.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*-------------------------------------------------------------------------
| Authenticated Routes
|------------------------------------------------------------------------*/
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/update-info', [ProfileController::class, 'update'])->name('update.info');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('update.password');
    });

    /*-------------------------------------------------------------------------
    | Admin Routes
    |------------------------------------------------------------------------*/
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /*-------------------------------------------------------------------------
        | Product Management Routes (Improved for modal-based CRUD)
        |------------------------------------------------------------------------*/
        Route::prefix('products')->name('products.')->group(function () {
            // Main CRUD Routes
            Route::get('/', [AdminProductController::class, 'index'])->name('index');
            Route::get('/create', [AdminProductController::class, 'create'])->name('create');
            Route::post('/', [AdminProductController::class, 'store'])->name('store');

            // Show route for AJAX (getting product data for modal)
            Route::get('/{product}', [AdminProductController::class, 'show'])->name('show');

            // Edit route with AJAX support
            Route::get('/{product}/edit', [AdminProductController::class, 'edit'])->name('edit');

            // Update route with proper HTTP methods
            Route::match(['PUT', 'PATCH', 'POST'], '/{product}', [AdminProductController::class, 'update'])
                ->name('update');

            // Delete route
            Route::delete('/{product}', [AdminProductController::class, 'destroy'])->name('destroy');

            // Additional utility routes for AJAX
            Route::get('/form/data', [AdminProductController::class, 'getFormData'])->name('form.data');

            // Stock management routes
            Route::post('/{id}/update-stock/{type}', [AdminProductController::class, 'updateStock'])
                ->name('update.stock')
                ->where('type', 'product|variant');

            /*-------------------------------------------------------------------------
            | Variant Management Routes (Optional - if you want separate variant endpoints)
            |------------------------------------------------------------------------*/
            Route::prefix('variants')->name('variants.')->group(function () {
                Route::post('/', [AdminProductController::class, 'storeVariant'])->name('store');
                Route::get('/{variant}', [AdminProductController::class, 'showVariant'])->name('show');
                Route::put('/{variant}', [AdminProductController::class, 'updateVariant'])->name('update');
                Route::delete('/{variant}', [AdminProductController::class, 'destroyVariant'])->name('destroy');
            });
        });

        /*-------------------------------------------------------------------------
        | Additional Admin Routes (for future expansion)
        |------------------------------------------------------------------------*/
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update.status');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
            Route::patch('/{user}/status', [AdminUserController::class, 'updateStatus'])->name('update.status');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminSettingController::class, 'index'])->name('index');
            Route::post('/', [AdminSettingController::class, 'store'])->name('store');
        });
    });

    /*-------------------------------------------------------------------------
    | Checkout & Order Routes
    |------------------------------------------------------------------------*/
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'showCheckoutForm'])->name('show');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
        Route::get('/pending', [CheckoutController::class, 'pending'])->name('pending');
        Route::get('/error', [CheckoutController::class, 'error'])->name('error');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [CheckoutController::class, 'listOrders'])->name('index');
        Route::get('/{order}', [CheckoutController::class, 'showOrder'])->name('show');
    });
});

/*-------------------------------------------------------------------------
| Payment Callback (excluded from CSRF protection)
|------------------------------------------------------------------------*/
Route::post('/midtrans-callback', [CheckoutController::class, 'midtransCallback'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('midtrans.callback');
