<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário autenticado é admin
        if (JWTAuth::user() || JWTAuth::user()->role !== 'admin') {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
