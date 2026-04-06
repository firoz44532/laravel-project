<?php

namespace App\Http\Middleware;

use App\Services\SuspiciousCustomerDetectionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedCustomer
{
    protected $detectionService;

    public function __construct(SuspiciousCustomerDetectionService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check order-related routes
        if ($this->isOrderRoute($request)) {
            $email = $this->getCustomerEmail($request);
            
            if ($email && $this->detectionService->isCustomerBanned($email)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account has been temporarily suspended. Please contact support for assistance.',
                        'error_type' => 'banned_customer'
                    ], 403);
                }
                
                return redirect()->route('home')
                    ->with('error', 'Your account has been temporarily suspended. Please contact support for assistance.');
            }
        }

        return $next($request);
    }

    private function isOrderRoute(Request $request)
    {
        $orderRoutes = [
            'checkout.store',
            'cart.add',
            'orders.store',
            'order.process'
        ];

        return in_array($request->route()?->getName(), $orderRoutes) ||
               str_contains($request->path(), 'checkout') ||
               str_contains($request->path(), 'order') ||
               str_contains($request->path(), 'cart/add');
    }

    private function getCustomerEmail(Request $request)
    {
        // Try to get email from authenticated user
        if (auth()->check() && auth()->user()->email) {
            return auth()->user()->email;
        }

        // Try to get email from request data
        if ($request->has('email')) {
            return $request->input('email');
        }

        // Try to get email from billing information
        if ($request->has('billing_email')) {
            return $request->input('billing_email');
        }

        // Try to get from checkout session or order data
        if ($request->has('customer') && isset($request->customer['email'])) {
            return $request->customer['email'];
        }

        return null;
    }
}
