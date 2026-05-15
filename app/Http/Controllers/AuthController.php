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
        'email'    => 'required|email',
        'password' => 'required',
        'role_id'  => 'required'
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        $selectedRole = $request->role_id;

        $isAuthorized = false;

        if ($selectedRole === 'pimpinan') {
            // Izinkan jika user adalah Kajur (3) atau Kaprodi (4)
            if (in_array($user->role_id, [3, 4])) {
                $isAuthorized = true;
            }
        } else {
            // Untuk Admin (2) atau Super (1), harus tepat sama
            if ($user->role_id == $selectedRole) {
                $isAuthorized = true;
            }
        }

        if ($isAuthorized) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        Auth::logout();
        return back()->withErrors(['email' => 'Role tidak sesuai.'])->withInput();
    }

    return back()->withErrors(['email' => 'Kredensial salah.'])->withInput();
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