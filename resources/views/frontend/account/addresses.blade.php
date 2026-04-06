@extends('frontend.layout')

@section('title', 'My Addresses')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-6">My Account</h2>
                <nav class="space-y-2">
                    <a href="{{ route('account.dashboard') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('account.profile') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="{{ route('account.addresses') }}" 
                       class="block px-4 py-2 rounded-lg bg-primary text-white">
                        <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                    </a>
                    <a href="{{ route('account.orders') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-shopping-bag mr-2"></i>Orders
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-lg hover:bg-gray-100 text-left">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b flex justify-between items-center">
                    <h1 class="text-2xl font-bold">My Addresses</h1>
                    <a href="{{ route('account.addresses.create') }}" 
                       class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                        <i class="fas fa-plus mr-2"></i>Add New Address
                    </a>
                </div>
                
                <div class="p-6">
                    @forelse($addresses as $address)
                        <div class="border rounded-lg p-4 mb-4 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="font-semibold text-lg">{{ $address->first_name }} {{ $address->last_name }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($address->type) }}
                                        </span>
                                        @if($address->is_default)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-phone mr-2"></i>
                                            {{ $address->phone }}
                                        </div>
                                        @if($address->email)
                                            <div class="flex items-center">
                                                <i class="fas fa-envelope mr-2"></i>
                                                {{ $address->email }}
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <i class="fas fa-home mr-2"></i>
                                            {{ $address->address_line_1 }}
                                            @if($address->address_line_2)
                                                , {{ $address->address_line_2 }}
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            {{ $address->city }}, {{ $address->division }}
                                            @if($address->postal_code)
                                                , {{ $address->postal_code }}
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-globe mr-2"></i>
                                            {{ $address->country }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2 ml-4">
                                    @if(!$address->is_default)
                                        <button onclick="setDefault({{ $address->id }})" 
                                                class="text-green-600 hover:text-green-800 text-sm">
                                            <i class="fas fa-star mr-1"></i>Set Default
                                        </button>
                                    @endif
                                    <a href="{{ route('account.addresses.edit', $address) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <button onclick="deleteAddress({{ $address->id }})" 
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-map-marker-alt text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Addresses Yet</h3>
                            <p class="text-gray-500 mb-4">Add your first address to get started with faster checkout</p>
                            <a href="{{ route('account.addresses.create') }}" 
                               class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                                Add Your First Address
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDefault(addressId) {
    fetch(`/account/addresses/${addressId}/set-default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error setting default address');
    });
}

function deleteAddress(addressId) {
    if (confirm('Are you sure you want to delete this address?')) {
        fetch(`/account/addresses/${addressId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting address');
        });
    }
}
</script>
@endsection
