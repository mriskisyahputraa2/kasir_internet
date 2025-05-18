<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika sudah login tapi mencoba akses halaman login
        if ($request->routeIs('login') && Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Jika belum login dan bukan di halaman login
        if (!Auth::check() && !$request->routeIs('login')) {
            return redirect()->route('login')
                ->withErrors(['loginError' => 'Silakan login terlebih dahulu.']);
        }

        // Share data user ke semua view
        View::share('user', Auth::user());

        return $next($request);
        // // Cek apakah session user ada
        // if (!session()->has('user')) {
        //     // Jika tidak ada session, cek remember me
        //     if (Auth::viaRemember()) {
        //         $user = Auth::user();
        //         session(['user' => $user]);
        //     } else {
        //         return redirect()->route('login')->withErrors(['loginError' => 'Silakan login terlebih dahulu.']);
        //     }
        // }

        // // Ambil user dari session
        // $user = session('user');

        // // Jika user sudah login, tidak boleh akses halaman login
        // if ($request->route()->getName() === 'login') {
        //     return redirect()->route('dashboard');
        // }

        // // Bagikan user ke semua views agar bisa diakses di semua halaman
        // View::share('user', $user);

        // return $next($request);
    }
}
