@extends('frontend.layout')

@section('title', 'Checkout')

@push('head')
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1234567890', {}, {debug: true}); // Replace with your Facebook Pixel ID
fbq('track', 'PageView');
fbq('track', 'InitiateCheckout', {
    content_ids: {{ isset($cart) ? json_encode($cart->items->pluck('product_id')) : '[]' }},
    content_type: 'product',
    value: {{ isset($cart) ? $total : 0 }},
    currency: 'BDT',
    num_items: {{ isset($cart) ? $cart->items->count() : 0 }}
});
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1234567890&ev=PageView&noscript=1"
src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"
/></noscript>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('pending_save_address_id') && !Auth::check())
        <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex items-start">
                <div class="flex-1">
                    <p class="text-sm text-yellow-800">You asked to save an address to your account. <a href="{{ route('login') }}" class="font-semibold underline">Login</a> or <a href="{{ route('register') }}" class="font-semibold underline">Register</a> to save it permanently.</p>
                </div>
            </div>
        </div>
    @endif
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Checkout Form -->
        <div class="flex-1">
            <form id="checkout-form">
                @csrf
                
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Shipping Address</h2>
                    
                    @auth
                        @if($addresses && $addresses->count() > 0)
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
                                                {{ $address->city }}, {{ $address->division ?? '' }}<br>
                                                {{ $address->country ?? 'Bangladesh' }}<br>
                                                Phone: {{ $address->phone }}
                                            </div>
                                            @if($address->is_default)
                                                <span class="inline-block mt-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Default</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                            <a href="{{ route('account.addresses.create', ['from' => 'checkout']) }}" class="text-primary hover:text-orange-600">
                                <i class="fas fa-plus mr-2"></i>Add New Address
                            </a>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-600 mb-4">No shipping addresses found</p>
                                <a href="{{ route('account.addresses.create', ['from' => 'checkout']) }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">Add Shipping Address</a>
                            </div>
                        @endif
                    @else
                        <!-- Guest inline shipping form -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="shipping_first_name" required class="w-full px-3 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="shipping_last_name" required class="w-full px-3 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone <span class="text-red-500">*</span></label>
                                    <input type="tel" name="shipping_phone" required class="w-full px-3 py-2 border rounded-lg" placeholder="01XXXXXXXXX">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="shipping_email" class="w-full px-3 py-2 border rounded-lg">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address Line 1 <span class="text-red-500">*</span></label>
                                <input type="text" name="shipping_address_line_1" required class="w-full px-3 py-2 border rounded-lg" placeholder="House/Flat/Street">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Address Line 2</label>
                                <input type="text" name="shipping_address_line_2" class="w-full px-3 py-2 border rounded-lg" placeholder="Area / Landmark (optional)">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City <span class="text-red-500">*</span></label>
                                    <input type="text" name="shipping_city" required class="w-full px-3 py-2 border rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Division <span class="text-red-500">*</span></label>
                                    <select name="shipping_division" required class="w-full px-3 py-2 border rounded-lg">
                                        <option value="">Select Division</option>
                                        <option>Barishal</option>
                                        <option>Chattogram</option>
                                        <option>Dhaka</option>
                                        <option>Khulna</option>
                                        <option>Mymensingh</option>
                                        <option>Rajshahi</option>
                                        <option>Rangpur</option>
                                        <option>Sylhet</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <input type="checkbox" id="billing_same" name="billing_same" checked class="mr-2">
                                <label for="billing_same" class="text-sm text-gray-700">Billing address same as shipping</label>
                            </div>

                            <div class="flex items-center space-x-3 mt-3">
                                <input type="checkbox" id="save_to_account" name="save_to_account" class="mr-2">
                                <label for="save_to_account" class="text-sm text-gray-700">Save this address to my account</label>
                            </div>

                            <div id="billing-fields" style="display:none;" class="mt-4">
                                <h4 class="font-semibold mb-3">Billing Address (optional)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                                        <input type="text" name="billing_first_name" class="w-full px-3 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                                        <input type="text" name="billing_last_name" class="w-full px-3 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                                        <input type="tel" name="billing_phone" class="w-full px-3 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="billing_email" class="w-full px-3 py-2 border rounded-lg">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700">Address Line 1</label>
                                    <input type="text" name="billing_address_line_1" class="w-full px-3 py-2 border rounded-lg">
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700">Address Line 2</label>
                                    <input type="text" name="billing_address_line_2" class="w-full px-3 py-2 border rounded-lg">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">City</label>
                                        <input type="text" name="billing_city" class="w-full px-3 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Division</label>
                                        <select name="billing_division" class="w-full px-3 py-2 border rounded-lg">
                                            <option value="">Select Division</option>
                                            <option>Barishal</option>
                                            <option>Chattogram</option>
                                            <option>Dhaka</option>
                                            <option>Khulna</option>
                                            <option>Mymensingh</option>
                                            <option>Rajshahi</option>
                                            <option>Rangpur</option>
                                            <option>Sylhet</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Cash on Delivery -->
                        <label class="flex items-center p-4 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cash_on_delivery" checked class="mr-3">
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
                        
                        <!-- Bank Transfer -->
                        <label class="flex items-center p-4 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="bank_transfer" class="mr-3">
                            <div>
                                <div class="font-medium">Bank Transfer</div>
                                <div class="text-sm text-gray-500">Direct bank payment</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Dynamic payment details -->
                <div id="payment-details" class="mt-4"></div>
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
                        <span>৳{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping</span>
                        <span>৳{{ number_format($shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax (VAT 15%)</span>
                        <span>৳{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="border-t pt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold text-primary">৳{{ number_format($total, 2) }}</span>
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
    // Helper: clear previous validation errors
    function clearValidationErrors(form) {
        form.querySelectorAll('.field-error').forEach(el => el.remove());
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    }

    // Helper: render validation errors (Laravel style: { field: [msg] })
    function renderValidationErrors(form, errors) {
        Object.keys(errors).forEach(function(field) {
            const messages = errors[field];
            // find inputs/selects/textarea by name (handles arrays like items[0][qty])
            const selector = '[name="' + field.replace(/\"/g, '\\"') + '"]';
            let inputs = Array.from(form.querySelectorAll(selector));
            // fallback: query by name starts-with (for array inputs)
            if (inputs.length === 0) {
                inputs = Array.from(form.querySelectorAll('[name^="' + field.split('[')[0] + '"]'));
            }
            if (inputs.length === 0) return;

            inputs.forEach(function(input) {
                input.classList.add('input-error','border-red-600','ring-1','ring-red-200');
                const err = document.createElement('p');
                err.className = 'field-error text-sm text-red-600 mt-1';
                err.innerText = messages.join(' ');
                // try to insert after input or its parent wrapper
                if (input.nextSibling) input.parentNode.insertBefore(err, input.nextSibling);
                else input.parentNode.appendChild(err);
            });
        });
    }

    // Handle form submission with field-level errors
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('place-order-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        clearValidationErrors(form);

        // Facebook Pixel - AddPaymentInfo event
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedPaymentMethod) {
            fbq('track', 'AddPaymentInfo', {
                content_ids: {{ isset($cart) ? json_encode($cart->items->pluck('product_id')) : '[]' }},
                content_type: 'product',
                value: parseFloat(document.querySelector('.text-lg.font-bold.text-primary').textContent.replace('৳', '').replace(',', '')),
                currency: 'BDT',
                payment_method: selectedPaymentMethod.value
            });
        }

        const formData = new FormData(this);

        fetch('{{ route("checkout.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (response.status === 422) {
                return response.json().then(errJson => {
                    renderValidationErrors(form, errJson.errors || {});
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Place Order';
                    throw new Error('validation');
                });
            }
            return response.json().then(data => ({ status: response.status, data }));
        })
        .then(({ data }) => {
            if (data.success) {
                // Facebook Pixel - Purchase event
                fbq('track', 'Purchase', {
                    content_ids: {{ isset($cart) ? json_encode($cart->items->pluck('product_id')) : '[]' }},
                    content_type: 'product',
                    value: parseFloat(document.querySelector('.text-lg.font-bold.text-primary').textContent.replace('৳', '').replace(',', '')),
                    currency: 'BDT',
                    transaction_id: data.order_id || 'ORDER_' + Date.now(),
                    num_items: {{ isset($cart) ? $cart->items->count() : 0 }}
                });

                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Unable to place order.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Place Order';
            }
        })
        .catch(error => {
            if (error.message === 'validation') return;
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Place Order';
        });
    });
});

function showAddAddressForm(type) {
    alert('Address creation form would open here. For now, please add addresses through your account section.');
}

// Toggle billing fields when guest unchecks 'billing_same'
document.addEventListener('DOMContentLoaded', function() {
    const billingCheckbox = document.getElementById('billing_same');
    const billingFields = document.getElementById('billing-fields');
    if (billingCheckbox) {
        billingCheckbox.addEventListener('change', function() {
            if (this.checked) billingFields.style.display = 'none';
            else billingFields.style.display = 'block';
        });
    }
});

// Dynamic payment details UI
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('payment-details');
    function renderFields(method) {
        container.innerHTML = '';
        if (!method) return;
        if (['bkash','nagad','rocket','upay'].includes(method)) {
            container.innerHTML = `
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700">Payment Mobile Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="payment_mobile" id="payment_mobile" required class="w-full px-3 py-2 border rounded-lg" placeholder="01XXXXXXXXX">
                </div>
            `;
        } else if (method === 'bank_transfer') {
            container.innerHTML = `
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700">Account Holder Name <span class="text-red-500">*</span></label>
                    <input type="text" name="account_holder_name" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                    <input type="text" name="bank_name" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Number</label>
                        <input type="text" name="bank_account_number" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Branch Name</label>
                        <input type="text" name="branch_name" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
            `;
        }
    }

    document.querySelectorAll('input[name="payment_method"]').forEach(function(r) {
        r.addEventListener('change', function() {
            renderFields(this.value);
        });
    });

    // render initially
    const initial = document.querySelector('input[name="payment_method"]:checked');
    if (initial) renderFields(initial.value);
});

// If a new address was just created from checkout, auto-select it and scroll to place-order
document.addEventListener('DOMContentLoaded', function() {
    var newAddressId = '{{ session("new_address_id") ?? "" }}';
    if (newAddressId) {
        try {
            var radio = document.querySelector('input[name="shipping_address_id"][value="' + newAddressId + '"]');
            if (radio) {
                radio.checked = true;
            }
            var placeBtn = document.getElementById('place-order-btn');
            if (placeBtn) {
                placeBtn.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        } catch (e) { console.error(e); }
    }
});
</script>
@endsection
