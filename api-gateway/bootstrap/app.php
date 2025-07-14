<?php

use App\Http\Middleware\ValidateToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        $middleware->alias([
            'validate.token' => ValidateToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Illuminate\Http\Exceptions\ThrottleRequestsException $e, Illuminate\Http\Request $request) {
            return response()->json([
                'message' => 'Demasiadas solicitudes. Intenta nuevamente en unos segundos.',
                'code' => 429
            ], 429);
        });
    })->create();
