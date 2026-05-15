<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CekSessionLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika tidak ada session 'is_logged_in', tendang kembali ke halaman login
        if (!Session::has('is_logged_in')) {
            return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        return $next($request);
    }
}