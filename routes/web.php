<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dalam grup middleware "web"
| yang berisi middleware grup "web". Buat sesuatu yang hebat!
|
*/

// Public Routes (Accessible by anyone)
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/404', [PageController::class, 'notFound'])->name('404');
Route::get('/collections', [ProductController::class, 'showCollections'])->name('collections');
Route::get('/shop-product/{id}', [ProductController::class, 'showProductDetail'])->name('shop.product.detail');

Route::get('/api/cities/{provinceId}', [CheckoutController::class, 'getCitiesByProvince'])->name('api.cities');

Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login Basic
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('login.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'add'])->name('add');
    Route::put('{cartItem}/update-quantity', [CartController::class, 'updateQuantity'])->name('update_quantity');
    Route::put('{cartItem}/update-variant', [CartController::class, 'updateVariant'])->name('update_variant');
    Route::delete('{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::post('clear', [CartController::class, 'clear'])->name('clear');
});


// Authenticated User Routes
Route::middleware('auth')->group(function () {

    Route::middleware('can:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    Route::middleware('role:customer')->group(function () {
        // Checkout Routes
        Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.show');
        Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');

        // Order Routes
        Route::get('/orders', [CheckoutController::class, 'listOrders'])->name('orders.index');
        Route::get('/orders/{orderCode}', [CheckoutController::class, 'showOrder'])->name('order.detail');
    });

    // Misalnya: Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
});

Route::post('/payment/callback', [CheckoutController::class, 'handleCallback'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('payment.callback');

Route::get('/checkout/success', [CheckoutController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('/checkout/pending', [CheckoutController::class, 'checkoutPending'])->name('checkout.pending');
Route::get('/checkout/error', [CheckoutController::class, 'checkoutError'])->name('checkout.error');
