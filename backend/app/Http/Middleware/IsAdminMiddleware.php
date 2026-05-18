<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     * Blocks non-admin authenticated users with a 403 Forbidden response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()?->is_admin) {
            return response()->json(['message' => 'Forbidden. Admin access required.'], 403);
        }

        return $next($request);
    }
}
