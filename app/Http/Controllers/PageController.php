<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // Method untuk halaman home
    public function index()
    {
        return view('home');
    }

    // Method untuk halaman
    public function process()
    {
        return view('process');
    }

    // Method untuk halaman checkout
    public function checkout()
    {
        return view('checkout');
    }
}
