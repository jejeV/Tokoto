<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/process', [PageController::class, 'process'])->name('process');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/collections', [ProductController::class, 'showCollections'])->name('collections');
Route::get('/shop-product/{id}', [ProductController::class, 'showProductDetail'])->name('shop.product.detail');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'add'])->name('add');
    Route::put('{cartItem}/update-quantity', [CartController::class, 'updateQuantity'])->name('update_quantity');
    Route::put('{cartItem}/update-variant', [CartController::class, 'updateVariant'])->name('update_variant');
    Route::delete('{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::post('clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/cart-count', [CartController::class, 'getCartCount'])->name('count');
    Route::post('/cart/validate', [CartController::class, 'validateCart'])->name('validate');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('login.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::middleware('can:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

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

Route::post('/midtrans-callback', [CheckoutController::class, 'midtransCallback'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('midtrans.callback');

