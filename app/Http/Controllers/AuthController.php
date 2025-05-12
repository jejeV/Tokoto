<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Menampilkan form login
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect sesuai role
        if (auth()->check()) {
            return $this->redirectAfterAuth();
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Jika sudah login, redirect sesuai role
        if (auth()->check()) {
            return $this->redirectAfterAuth();
        }

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return $this->redirectAfterAuth();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan form registrasi
     */
    public function showRegistrationForm()
    {
        // Jika sudah login, redirect ke home
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        // Jika sudah login, redirect ke home
        if (auth()->check()) {
            return redirect()->route('home');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer'
        ]);

        // Event untuk verifikasi email (jika diperlukan)
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home')
               ->with('success', 'Registrasi berhasil! Selamat datang '.$user->name);
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        // Jika belum login, redirect ke login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
               ->with('status', 'Anda telah berhasil logout');
    }

    /**
     * Helper untuk redirect setelah auth
     */
    protected function redirectAfterAuth()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                   ->with('success', 'Selamat datang Admin!');
        }

        return redirect()->route('home')
               ->with('success', 'Selamat datang kembali!');
    }
}
