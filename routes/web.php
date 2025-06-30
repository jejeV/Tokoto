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
use App\Http\Controllers\Admin\OrderController as AdminOrderController; // Alias for admin order controller

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// --- Public Routes ---
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/collections', [ProductController::class, 'showCollections'])->name('collections');
Route::get('/shop-product/{id}', [ProductController::class, 'showProductDetail'])->name('shop.product.detail');

// --- Cart Routes ---
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

// --- Authentication Routes (Guest Middleware) ---
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth Routes
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('login.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

// Logout Route (can be accessed by authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Authenticated Routes (Auth Middleware) ---
Route::middleware('auth')->group(function () {

    // --- Profile Routes ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/update-info', [ProfileController::class, 'update'])->name('update.info');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('update.password');
    });

    // --- Checkout & Order Routes (Customer) ---
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'showCheckoutForm'])->name('show');
        Route::post('/process', [CheckoutController::class, 'process'])->name('process');

        Route::post('/midtrans-success-callback', [CheckoutController::class, 'handleMidtransSuccess'])->name('midtrans_success');
        Route::post('/midtrans-pending-callback', [CheckoutController::class, 'handleMidtransPending'])->name('midtrans_pending');
        Route::post('/midtrans-error-callback', [CheckoutController::class, 'handleMidtransError'])->name('midtrans_error');
        Route::post('/midtrans-popup-close', [CheckoutController::class, 'handlePopupClose'])->name('midtrans_popup_close');

        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
        Route::get('/pending', [CheckoutController::class, 'pending'])->name('pending');
        Route::get('/error', [CheckoutController::class, 'error'])->name('error');
    });

    // Customer Order History
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [CheckoutController::class, 'listOrders'])->name('index');
        Route::get('/{order}', [CheckoutController::class, 'showOrder'])->name('show');
    });

    // --- Admin Routes (Admin Middleware) ---
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Product Management Routes
        Route::resource('products', AdminProductController::class);
        Route::post('products/{id}/update-stock/{type}', [AdminProductController::class, 'updateStock'])
            ->name('products.update.stock')
            ->where('type', 'product|variant');

        // Variant Management
        Route::prefix('variants')->name('variants.')->group(function () {
            Route::post('/', [AdminProductController::class, 'storeVariant'])->name('store');
            Route::get('/{variant}', [AdminProductController::class, 'showVariant'])->name('show');
            Route::put('/{variant}', [AdminProductController::class, 'updateVariant'])->name('update');
            Route::delete('/{variant}', [AdminProductController::class, 'destroyVariant'])->name('destroy');
        });
        // Additional product related routes
        Route::get('products/form/data', [AdminProductController::class, 'getFormData'])->name('products.form.data');

        // Order Management Routes
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::get('/{order}/invoice', [AdminOrderController::class, 'generateInvoice'])->name('invoice');

            Route::delete('/{order}', [AdminOrderController::class, 'destroy'])->name('destroy');
            Route::post('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('updatestatus');
            Route::post('/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('update.payment-status');
            Route::get('/export', [AdminOrderController::class, 'exportExcel'])->name('export');
        });
    });
});

