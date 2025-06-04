<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Penting untuk memeriksa autentikasi user

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role  // Parameter untuk role (misal 'admin', 'customer')
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            // Jika belum login, redirect ke halaman login
            return redirect('/login');
        }

        $user = Auth::user();

        // Periksa apakah role user sesuai dengan role yang diminta
        if ($user->role !== $role) {
            // Jika tidak sesuai, redirect ke halaman tertentu atau tampilkan error 403
            // Contoh: Redirect ke halaman home dengan pesan error
            return redirect('/home')->with('error', 'Anda tidak memiliki akses untuk halaman ini.');
            // Atau: abort(403, 'Unauthorized action.');
        }

        // Jika user memiliki role yang sesuai, lanjutkan request
        return $next($request);
    }
}
