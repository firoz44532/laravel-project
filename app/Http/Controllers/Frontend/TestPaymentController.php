<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class TestPaymentController extends Controller
{
    /**
     * Show a sandbox payment page that lets testers approve or decline.
     * Only available in non-production environments.
     */
    public function show(Request $request, string $transactionId)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $payment = Payment::where('transaction_id', $transactionId)
            ->with('order')
            ->firstOrFail();

        return view('frontend.checkout.test-payment', compact('payment'));
    }

    /**
     * Handle sandbox payment decision (approve / decline).
     */
    public function process(Request $request, string $transactionId)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $request->validate([
            'action' => 'required|in:approve,decline',
        ]);

        $payment = Payment::where('transaction_id', $transactionId)
            ->with('order')
            ->firstOrFail();

        $callbackUrl = route('payment.callback', [
            'gateway' => $payment->method,
            'transaction_id' => $transactionId,
            'status' => $request->action === 'approve' ? 'success' : 'failed',
        ]);

        return redirect($callbackUrl);
    }
}
