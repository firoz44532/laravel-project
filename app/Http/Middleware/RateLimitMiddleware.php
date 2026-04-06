<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Different rate limits for different endpoints
        $key = $this->getRequestKey($request);
        $maxAttempts = $this->getMaxAttempts($request);
        $decayMinutes = $this->getDecayMinutes($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        RateLimiter::hit($key);

        return $next($request);
    }

    private function getRequestKey(Request $request): string
    {
        $ip = $request->ip();
        $userId = auth()->id();
        $endpoint = $request->route()->getName();

        if ($userId) {
            return "rate_limit:{$endpoint}:user:{$userId}";
        }

        return "rate_limit:{$endpoint}:ip:{$ip}";
    }

    private function getMaxAttempts(Request $request): int
    {
        $endpoint = $request->route()->getName();

        // Different limits for different endpoints
        $limits = [
            'login' => 5,        // 5 login attempts per minute
            'register' => 3,     // 3 registration attempts per minute
            'cart.add' => 30,    // 30 add to cart attempts per minute
            'cart.update' => 30, // 30 cart updates per minute
            'checkout.store' => 5, // 5 checkout attempts per minute
            'contact' => 10,     // 10 contact form submissions per hour
        ];

        // Default limit
        return $limits[$endpoint] ?? 60;
    }

    private function getDecayMinutes(Request $request): int
    {
        $endpoint = $request->route()->getName();

        // Different decay times for different endpoints
        $decays = [
            'login' => 1,         // 1 minute
            'register' => 1,      // 1 minute
            'cart.add' => 1,      // 1 minute
            'cart.update' => 1,   // 1 minute
            'checkout.store' => 1, // 1 minute
            'contact' => 60,      // 1 hour
        ];

        // Default decay time
        return $decays[$endpoint] ?? 1;
    }
}
