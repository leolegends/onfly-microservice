<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isActive()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
        }

        return $next($request);
    }
}
