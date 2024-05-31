<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Application;

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'auth' => \App\Http\Middleware\AuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {


        $exceptions->render(function (Throwable $e, Request $request) {

            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return apiResponse(false, [], $e->getMessage(), 422);
                }

                $error = env("APP_DEBUG") ? [
                    "file" => $e->getFile(),
                    "line" => $e->getLine()
                ] : [];


                $status_code = (empty($e->getCode()) || $e->getCode() == 0) ? 500 : $e->getCode();
                return apiResponse(false, $error, $e->getMessage(), $status_code);
            }
        });
    })->create();
