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
        $middleware->validateCsrfTokens(except: [
            url('payment/callback'),
        ]);

        // Register route middleware aliases
        $middleware->alias([
            'validate.webhook' => \App\Http\Middleware\ValidateWebhookSignature::class,
            'client.isolation' => \App\Http\Middleware\EnsureClientIsolation::class,
            'scanner.role' => \App\Http\Middleware\EnsureScannerRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
