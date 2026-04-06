@extends('frontend.layout')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Checkout Form -->
        <div class="flex-1">
            <form id="checkout-form">
                @csrf
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Shipping Address</h2>
                    
                    @if($addresses->count() > 0)
                        <div class="space-y-3 mb-4">
                            @foreach($addresses as $address)
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="shipping_address_id" value="{{ $address->id }}" 
                                           {{ $defaultAddress && $defaultAddress->id == $address->id ? 'checked' : '' }}
                                           class="mt-1 mr-3">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $address->first_name }} {{ $address->last_name }}</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            {{ $address->address_line_1 }}<br>
                                            @if($address->address_line_2){{ $address->address_line_2 }}<br>@endif
                                            {{ $address->city }}, {{ $address->division }}<br>
                                            {{ $address->country }}<br>
                                            Phone: {{ $address->phone }}
                                        </div>
                                        @if($address->is_default)
                                            <span class="inline-block mt-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Default</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        
                        <button type="button" onclick="showAddAddressForm('shipping')" 
                                class="text-primary hover:text-orange-600">
                            <i class="fas fa-plus mr-2"></i>Add New Address
                        </button>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-600 mb-4">No shipping addresses found</p>
                            <button type="button" onclick="showAddAddressForm('shipping')" 
                                    class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                                Add Shipping Address
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Billing Address -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Billing Address</h2>
                    
                    <label class="flex items-center mb-4">
                        <input type="checkbox" id="same_as_shipping" checked class="mr-2">
                        <span>Same as shipping address</span>
                    </label>
                    
                    <div id="billing-address-section" class="hidden">
                        @if($addresses->count() > 0)
                            <div class="space-y-3">
                                @foreach($addresses as $address)
                                    <label class="flex items-start p-3 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="billing_address_id" value="{{ $address->id }}" 
                                               class="mt-1 mr-3">
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $address->first_name }} {{ $address->last_name }}</div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ $address->address_line_1 }}<br>
                                                @if($address->address_line_2){{ $address->address_line_2 }}<br>@endif
                                                {{ $address->city }}, {{ $address->division }}<br>
                                                {{ $address->country }}<br>
                                                Phone: {{ $address->phone }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-600 mb-4">No billing addresses found</p>
                                <button type="button" onclick="showAddAddressForm('billing')" 
                                        class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                                    Add Billing Address
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Cash on Delivery -->
                        <label class="flex items-center p-4 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" checked class="mr-3">
                            <div>
                                <div class="font-medium">Cash on Delivery</div>
                                <div class="text-sm text-gray-500">Pay when you receive</div>
                            </div>
                        </label>
                        
                        <!-- bKash -->
                        <label class="flex items-center p-4 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="bkash" class="mr-3">
                            <div>
                                <div class="font-medium">bKash</div>
                                <div class="text-sm text-gray-500">Mobile banking</div>
                            </div>
                        </label>
                        
                        <!-- Nagad -->
                        <label class="flex items-center p-4 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="nagad" class="mr-3">
                            <div>
                                <div class="font-medium">Nagad</div>
                                <div class="text-sm text-gray-500">Mobile banking</div>
                            </div>
                        </label>
                        
                        <!-- Rocket -->
                        <label class="flex items-center p-4 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="rocket" class="mr-3">
                            <div>
                                <div class="font-medium">Rocket</div>
                                <div class="text-sm text-gray-500">Mobile banking</div>
                            </div>
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-96">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                
                <!-- Cart Items -->
                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                    @foreach($cartItems as $item)
                        <div class="flex items-center gap-3">
                            @if($item->primaryImage)
                                <img src="{{ $item->primaryImage->image_url }}" 
                                     alt="{{ $item->name }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-sm"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="text-sm font-medium line-clamp-1">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ $item->quantity }}</div>
                            </div>
                            <div class="text-sm font-semibold">৳{{ number_format($item->price * $item->quantity, 2) }}</div>
                        </div>
                    @endforeach
                </div>

                <!-- Price Breakdown -->
                <div class="pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span>৳<span id="checkout-subtotal">{{ number_format($subtotal, 2) }}</span></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span>৳<span id="checkout-shipping">{{ number_format($shipping, 2) }}</span></span>
                    </div>
                    <div class="flex justify-between text-sm" id="tax-row">
                        <span class="text-gray-600">Tax (VAT <span id="vat-rate-display">15</span>%)</span>
                        <span>৳<span id="checkout-tax">{{ number_format($tax, 2) }}</span></span>
                    </div>
                    <div class="border-t pt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold text-primary">৳<span id="checkout-total">{{ number_format($total, 2) }}</span></span>
                        </div>
                    </div>
                </div>

                <!-- Security Note -->
                <div class="mt-4 text-center text-xs text-gray-500">
                    <i class="fas fa-lock mr-1"></i>
                    Secure checkout powered by SSL encryption
                </div>
                
                <!-- Place Order Button -->
                <button type="submit" form="checkout-form" id="place-order-btn" 
                        class="w-full mt-6 bg-primary text-white py-3 rounded-lg hover:bg-orange-600 transition font-medium">
                    Place Order
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle same as shipping checkbox
    document.getElementById('same_as_shipping').addEventListener('change', function() {
        const billingSection = document.getElementById('billing-address-section');
        billingSection.classList.toggle('hidden', this.checked);
        
        if (this.checked) {
            // Copy shipping address selection to billing
            const shippingRadio = document.querySelector('input[name="shipping_address_id"]:checked');
            if (shippingRadio) {
                const billingRadio = document.querySelector(`input[name="billing_address_id"][value="${shippingRadio.value}"]`);
                if (billingRadio) billingRadio.checked = true;
            }
        }
    });

    // Handle form submission
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('place-order-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        
        const formData = new FormData(this);
        
        fetch('{{ route("checkout.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Place Order';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Place Order';
        });
    });
});

function showAddAddressForm(type) {
    // This would open a modal or redirect to address creation page
    alert('Address creation form would open here. For now, please add addresses through your account section.');
}
</script>
@endsection
