<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FakeCustomerDetectionService;
use Symfony\Component\HttpFoundation\Response;

class FakeCustomerDetectionMiddleware
{
    protected FakeCustomerDetectionService $detectionService;

    public function __construct(FakeCustomerDetectionService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to registration POST requests (actual registration attempts)
        if (!$request->isMethod('POST') || (!$request->is('register') && !$request->routeIs('register'))) {
            return $next($request);
        }

        $ipAddress = $request->ip();

        // Check if IP is already blocked
        if ($this->detectionService->isBlockedIp($ipAddress)) {
            return response()->json([
                'message' => 'Access denied. Your IP address has been temporarily blocked due to suspicious activity.',
                'retry_after' => 3600 // 1 hour
            ], 403);
        }

        // Get registration data
        $userData = [
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
        ];

        // Perform fake customer detection
        $detection = $this->detectionService->detectFakeCustomer($request, $userData);

        // Block high-risk registrations
        if ($detection['should_block']) {
            $this->detectionService->blockIp($ipAddress, 60); // Block for 1 hour
            
            return response()->json([
                'message' => 'Registration blocked due to suspicious activity. Please contact support if you believe this is an error.',
                'risk_score' => $detection['risk_score'],
                'flags' => $detection['flags']
            ], 403);
        }

        // Add detection data to request for later use
        $request->merge([
            'fake_customer_detection' => $detection
        ]);

        return $next($request);
    }
}
