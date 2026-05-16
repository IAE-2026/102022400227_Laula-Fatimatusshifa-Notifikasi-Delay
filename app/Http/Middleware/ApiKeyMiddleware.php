<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-IAE-KEY');

        if ($apiKey !== '102022400227') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'errors' => null
            ], 401);
        }

        return $next($request);
    }
}