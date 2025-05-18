<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperadminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login, arahkan ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Jika bukan superadmin, arahkan ke dashboard dengan pesan error
        if (Auth::user()->role !== 'superadmin') {
            return redirect()->route('dashboard')
                ->with('error', 'Akses hanya untuk Superadmin');
        }

        return $next($request);
    }
}
