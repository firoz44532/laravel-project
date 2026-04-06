@extends('frontend.layout')

@section('title', 'Test Payment - ' . $payment->method_display_name)

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md text-center">
        <div class="mb-6">
            <div class="w-16 h-16 mx-auto bg-orange-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-credit-card text-2xl text-primary"></i>
            </div>
            <h1 class="text-2xl font-bold mb-1">Sandbox Payment</h1>
            <p class="text-sm text-gray-500">This is a test environment — no real charges.</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Method</span>
                <span class="font-medium">{{ $payment->method_display_name }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Order</span>
                <span class="font-medium">#{{ $payment->order->order_number }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Amount</span>
                <span class="font-bold text-primary">৳{{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Transaction ID</span>
                <span class="font-mono text-xs">{{ $payment->transaction_id }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('test-payment.process', $payment->transaction_id) }}">
            @csrf
            <div class="flex gap-3">
                <button type="submit" name="action" value="approve"
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    <i class="fas fa-check mr-1"></i> Approve
                </button>
                <button type="submit" name="action" value="decline"
                        class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-medium">
                    <i class="fas fa-times mr-1"></i> Decline
                </button>
            </div>
        </form>

        <p class="mt-4 text-xs text-gray-400">
            <i class="fas fa-lock mr-1"></i> Test-only — disabled in production
        </p>
    </div>
</div>
@endsection
