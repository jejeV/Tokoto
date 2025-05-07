<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // Method untuk halaman home
    public function home()
    {
        return view('home');
    }

    // Method untuk halaman
    public function cart()
    {
        return view('cart');
    }

    // Method untuk halaman checkout
    public function checkout()
    {
        return view('checkout');
    }
}
