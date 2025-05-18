<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckToko
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('login'); // Redirect ke login jika belum login
        }

        // Superadmin bebas masuk ke semua halaman
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Admin dan kasir tidak perlu memilih toko, langsung ke absen
        if ($user->role === 'admin' || $user->role === 'kasir') {
            return $next($request);
        }

        // Jika role lain dan belum memilih toko, arahkan ke pilih-toko
        if (!session()->has('id_toko')) {
            return redirect()->route('pilih-toko');
        }

        return $next($request);
    }
}
