<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Konstruktor untuk menerapkan middleware 'auth'.
     * Ini memastikan hanya pengguna yang login yang dapat mengakses metode di controller ini.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman dashboard akun pengguna yang juga berisi form edit profil dan ganti password.
     * Mengarahkan ke view 'profile.blade.php'.
     *
     * @return \Illuminate->View->View
     */
    public function index()
    {
        $user = Auth::user(); // Mendapatkan pengguna yang sedang login
        // Load relasi 'orders' jika Anda ingin menampilkan pesanan terbaru
        // Pastikan relasi ini didefinisikan di model User
        $user->load('orders');
        return view('profile', compact('user')); // Mengarahkan ke view 'profile.blade.php' langsung
    }

    /**
     * Memperbarui informasi profil pengguna (nama, email, telepon, alamat).
     *
     * @param  \Illuminate->Http->Request  $request
     * @return \Illuminate->Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi data yang masuk untuk profil
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', // Asumsi nama lengkap di 'name'
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Email harus unik kecuali untuk ID pengguna ini
            'phone_number' => 'nullable|string|max:20', // Contoh kolom tambahan
            'address' => 'nullable|string|max:255', // Contoh kolom tambahan
        ]);

        try {
            $user->update($validatedData);
            return redirect()->route('profile.index')->with('success', 'Informasi pribadi berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui informasi pribadi: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui password pengguna.
     *
     * @param  \Illuminate->Http\Request  $request
     * @return \Illuminate->Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Validasi data untuk password
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini tidak cocok.');
                }
            }],
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->route('profile.index')->with('success', 'Password berhasil diubah!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }
}
