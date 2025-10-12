<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\CheckLogout;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'CheckLogin' => CheckLogin::class,
            'CheckLogout' => CheckLogout::class
        ]); 
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
