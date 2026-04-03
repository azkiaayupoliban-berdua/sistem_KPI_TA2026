<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login
     */
   public function showLogin()
{
    // Jika ada sisa-sisa login lama, kita bersihkan dulu
    // agar halaman login BISA muncul
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    return view('auth.login');
}

    /**
     * Memproses data login dari form
     */
  public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();

        // LOGIKA FILTER HALAMAN UTAMA
        // Jika Admin (1) atau Super Admin (2), arahkan ke Dashboard Manajemen
        if (in_array($user->role_id, [1, 2])) {
            return redirect()->intended('/dashboard');
        }

        // JIKA KAPRODI / KAJUR:
        // Jangan arahkan ke /dashboard, tapi langsung ke /analytics
        // Ini membuat mereka seolah-olah tidak punya halaman 'Manajemen Antrean'
        return redirect()->route('dashboard.analytics');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}

    /**
     * Memproses logout (keluar sistem)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session dan token untuk keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman login
        return redirect('/login');
    }
}
