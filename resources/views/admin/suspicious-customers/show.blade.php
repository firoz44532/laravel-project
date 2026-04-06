@extends('admin.layout')

@section('title', 'Suspicious Customer Details')
@section('header', 'Customer Details: ' . $suspiciousCustomer->email)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Customer Overview -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $suspiciousCustomer->email }}</h1>
                        @if($suspiciousCustomer->isCurrentlyBanned())
                            <span class="ml-3 px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-ban mr-1"></i>BANNED
                            </span>
                        @else
                            <span class="ml-3 px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>ACTIVE
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-medium">{{ $suspiciousCustomer->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p class="font-medium">{{ $suspiciousCustomer->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">IP Address</p>
                            <p class="font-medium">{{ $suspiciousCustomer->ip_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Detection Method</p>
                            <p class="font-medium">{{ $suspiciousCustomer->detection_method ?? 'unknown' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col space-y-2 ml-6">
                    @if(!$suspiciousCustomer->isCurrentlyBanned())
                        <button onclick="showBanModal()" 
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-ban mr-2"></i>Ban Customer
                        </button>
                    @else
                        <button onclick="unbanCustomer()" 
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Unban Customer
                        </button>
                    @endif
                    
                    <a href="{{ route('admin.suspicious-customers.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Assessment -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Risk Assessment</h3>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Risk Score</span>
                        <span class="text-2xl font-bold text-{{ $suspiciousCustomer->risk_level_color }}-600">
                            {{ $suspiciousCustomer->risk_score }}/100
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-{{ $suspiciousCustomer->risk_level_color }}-600 h-3 rounded-full" 
                             style="width: {{ $suspiciousCustomer->risk_score }}%"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Risk Level: {{ ucfirst($suspiciousCustomer->risk_level) }}</p>
                </div>
                
                @if($suspiciousCustomer->risk_factors && count($suspiciousCustomer->risk_factors) > 0)
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Risk Factors:</h4>
                        <div class="space-y-2">
                            @foreach($suspiciousCustomer->risk_factors as $factor)
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $factor)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Order Statistics</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <p class="text-3xl font-bold text-red-600">{{ $suspiciousCustomer->fake_order_count }}</p>
                        <p class="text-sm text-gray-600">Fake Orders</p>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <p class="text-3xl font-bold text-yellow-600">{{ $suspiciousCustomer->cancelled_order_count }}</p>
                        <p class="text-sm text-gray-600">Cancelled Orders</p>
                    </div>
                </div>
                
                @if($suspiciousCustomer->is_banned && $suspiciousCustomer->banned_until)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-clock mr-2"></i>
                            Banned until: {{ $suspiciousCustomer->banned_until->format('M j, Y \a\t g:i A') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Account (if exists) -->
        @if($user)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Account</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account ID:</span>
                        <span class="text-sm font-medium">#{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Registered:</span>
                        <span class="text-sm font-medium">{{ $user->created_at->format('M j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Account Status:</span>
                        <span class="text-sm font-medium">
                            @if($user->is_active)
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Total Orders:</span>
                        <span class="text-sm font-medium">{{ $user->orders->count() }}</span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.users.show', $user->id) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-external-link-alt mr-1"></i>View User Account
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- IP Matches -->
        @if($ipMatches->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Same IP Address</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($ipMatches as $match)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $match->email }}</p>
                                <p class="text-xs text-gray-500">Risk: {{ $match->risk_score }}/100</p>
                            </div>
                            <a href="{{ route('admin.suspicious-customers.show', $match) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                View
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Recent Orders -->
    @if($orders->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Orders (Last 10)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $order->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ৳{{ number_format($order->total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ 
                                    $order->status == 'completed' ? 'green' : 
                                    ($order->status == 'cancelled' ? 'red' : 'yellow') 
                                }}-100 text-{{ 
                                    $order->status == 'completed' ? 'green' : 
                                    ($order->status == 'cancelled' ? 'red' : 'yellow') 
                                }}-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Admin Notes -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Admin Notes</h3>
        </div>
        <div class="p-6">
            @if($suspiciousCustomer->admin_notes)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ $suspiciousCustomer->admin_notes }}</pre>
                </div>
            @else
                <p class="text-gray-500">No admin notes available.</p>
            @endif
            
            <form method="POST" action="{{ route('admin.suspicious-customers.update-notes', $suspiciousCustomer) }}" 
                  class="mt-4">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Notes</label>
                    <textarea name="notes" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Add notes about this customer...">{{ $suspiciousCustomer->admin_notes }}</textarea>
                </div>
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Update Notes
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Ban Modal -->
<div id="banModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ban Customer</h3>
            <form id="banForm" method="POST" action="{{ route('admin.suspicious-customers.ban', $suspiciousCustomer) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Enter reason for banning this customer..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ban Until (optional)</label>
                    <input type="datetime-local" name="banned_until" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Leave empty for permanent ban</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBanModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Ban Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showBanModal() {
    document.getElementById('banModal').classList.remove('hidden');
}

function closeBanModal() {
    document.getElementById('banModal').classList.add('hidden');
    document.getElementById('banForm').reset();
}

function unbanCustomer() {
    if (confirm('Are you sure you want to unban this customer?')) {
        fetch('{{ route('admin.suspicious-customers.unban', $suspiciousCustomer) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while unbanning the customer.');
        });
    }
}
</script>
@endsection
