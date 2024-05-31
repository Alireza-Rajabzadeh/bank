<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];


        if (Auth::attempt($credentials)) {
            return $next($request);
        }

        throw new Exception(__("validation.auth_faield"), 401);
    }
}
