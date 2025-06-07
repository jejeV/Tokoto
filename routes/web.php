<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\DashboardController;

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

// --- Route Halaman Umum (Public) ---
Route::get('/', [PageController::class, 'index'])->name('home'); // Halaman utama
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/404', [PageController::class, 'notFound'])->name('404');
Route::get('/collections', [ProductController::class, 'showCollections'])->name('collections'); // Halaman daftar produk
Route::get('/shop-product/{id}', [ProductController::class, 'showProductDetail'])->name('shop.product.detail'); // Halaman detail produk

// --- Route Keranjang (Cart) ---
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');


// --- Route Autentikasi (Guest Middleware) ---
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

// --- Route Logout ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- Route yang Memerlukan Autentikasi (auth Middleware) ---
Route::middleware(['auth'])->group(function () {
    Route::middleware('can:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    // --- Route Cart Actions (Memerlukan auth DAN role customer)
    Route::post('/cart/add', [CartController::class, 'add'])
        ->middleware('role:customer') // Hanya customer yang bisa menambah
        ->name('cart.add'); // Mengubah nama route agar lebih konsisten, dari add.to.cart

    Route::post('/cart/update', [CartController::class, 'update'])
        ->middleware('role:customer') // Hanya customer yang bisa update
        ->name('cart.update');

    Route::post('/cart/remove', [CartController::class, 'remove'])
        ->middleware('role:customer') // Hanya customer yang bisa remove
        ->name('cart.remove');

    Route::post('/cart/clear', [CartController::class, 'clear'])
        ->middleware('role:customer') // Hanya customer yang bisa clear
        ->name('cart.clear');
});


