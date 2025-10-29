<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoggingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $logData = [
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('api')->info('API Request', $logData);

        $response = $next($request);

        Log::channel('api')->info('API Response', [
            'status' => $response->status(),
            'user_id' => $user ? $user->id : null,
        ]);

        return $response;
    }
}
