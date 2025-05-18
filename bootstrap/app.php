<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware aliases
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class, // Diubah ke bawaan Laravel
            'auth.session' => \App\Http\Middleware\AuthMiddleware::class,
            'check.toko' => \App\Http\Middleware\CheckToko::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'superadmin' => \App\Http\Middleware\SuperadminMiddleware::class,
        ]);

        // Middleware groups
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class, // Gunakan yang bawaan
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, // Gunakan yang bawaan
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetTimezoneMiddleware::class, // Tambahkan ini
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();