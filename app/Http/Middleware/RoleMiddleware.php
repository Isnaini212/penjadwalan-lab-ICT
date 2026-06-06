<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }
        
        abort(403, 'Ente bukan ' . strtoupper($role) . ' Bre! Jangan ngadi-ngadi.');
    }
}
