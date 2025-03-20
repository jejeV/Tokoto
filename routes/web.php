<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// Halaman Utama
Route::get('/', [PageController::class, 'home'])->name('home');

// Halaman About
Route::get('/about', [PageController::class, 'about'])->name('about');

// Halaman Shop
Route::get('/shop', [PageController::class, 'shop'])->name('shop');

// Halaman Single Product
Route::get('/product/{id}', [PageController::class, 'singleProduct'])->name('single-product');

// Halaman Cart
Route::get('/cart', [PageController::class, 'cart'])->name('cart');

// Halaman Checkout
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');

// Halaman Contact
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Halaman News
Route::get('/news', [PageController::class, 'news'])->name('news');

// Halaman 404
Route::get('/404', [PageController::class, 'notFound'])->name('404');
