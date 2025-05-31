<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\DashboardController;

// Halaman Publik (bisa diakses siapa saja)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/product/{id}', [PageController::class, 'singleProduct'])->name('single-product');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/404', [PageController::class, 'notFound'])->name('404');

// Authentication Routes
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

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'can:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

// Test
Route::get('/collect', function () {
    return view('collections');
});
