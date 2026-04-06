@extends('admin.layout')

@section('title', 'Suspicious Customers')
@section('header', 'Suspicious Customer Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Flagged</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_flagged'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">High Risk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['high_risk'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-exclamation-circle text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Medium Risk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['medium_risk'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-gray-100 rounded-full">
                    <i class="fas fa-ban text-gray-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Banned</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['banned'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-shopping-cart text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Fake Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_fake_orders'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filters</h3>
        </div>
        <form method="GET" action="{{ route('admin.suspicious-customers.index') }}" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Email, Name, or Phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Risk Level</label>
                    <select name="risk_level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Levels</option>
                        <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High Risk</option>
                        <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium Risk</option>
                        <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low Risk</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    @if($suspiciousCustomers->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4">
            <form method="POST" action="{{ route('admin.suspicious-customers.bulk-action') }}" id="bulkActionForm">
                @csrf
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="selectAll" class="text-sm text-gray-700">Select All</label>
                        
                        <select name="action" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Bulk Actions</option>
                            <option value="ban">Ban Selected</option>
                            <option value="unban">Unban Selected</option>
                        </select>
                        
                        <input type="text" name="reason" placeholder="Reason (required for ban)" 
                               class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            Apply
                        </button>
                    </div>
                    
                    <a href="{{ route('admin.suspicious-customers.create') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Suspicious Customer
                    </a>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Suspicious Customers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($suspiciousCustomers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="select-all-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fake Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detection</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($suspiciousCustomers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" 
                                           class="customer-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->email }}</div>
                                        @if($customer->name)
                                            <div class="text-sm text-gray-500">{{ $customer->name }}</div>
                                        @endif
                                        @if($customer->phone)
                                            <div class="text-sm text-gray-500">{{ $customer->phone }}</div>
                                        @endif
                                        @if($customer->ip_address)
                                            <div class="text-xs text-gray-400">IP: {{ $customer->ip_address }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $customer->risk_score }}</span>
                                        <span class="ml-2 px-2 py-1 text-xs rounded-full bg-{{ $customer->risk_level_color }}-100 text-{{ $customer->risk_level_color }}-800">
                                            {{ $customer->risk_level }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $customer->fake_order_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($customer->isCurrentlyBanned())
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-ban mr-1"></i>Banned
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $customer->detection_method ?? 'unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.suspicious-customers.show', $customer) }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    
                                    @if(!$customer->isCurrentlyBanned())
                                        <button onclick="banCustomer({{ $customer->id }})" 
                                                class="text-red-600 hover:text-red-900">Ban</button>
                                    @else
                                        <button onclick="unbanCustomer({{ $customer->id }})" 
                                                class="text-green-600 hover:text-green-900">Unban</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $suspiciousCustomers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-user-shield text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No suspicious customers found</h3>
                <p class="text-gray-500 mb-6">No customers match your current filters.</p>
                <a href="{{ route('admin.suspicious-customers.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Clear Filters
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Ban Customer Modal -->
<div id="banModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ban Customer</h3>
            <form id="banForm" method="POST">
                @csrf
                <input type="hidden" name="customer_id" id="banCustomerId">
                
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
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
    
    selectAllCheckbox?.addEventListener('change', function() {
        customerCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            selectAllCheckbox.checked = Array.from(customerCheckboxes).every(cb => cb.checked);
        });
    });
});

function banCustomer(customerId) {
    document.getElementById('banCustomerId').value = customerId;
    document.getElementById('banModal').classList.remove('hidden');
}

function closeBanModal() {
    document.getElementById('banModal').classList.add('hidden');
    document.getElementById('banForm').reset();
}

function unbanCustomer(customerId) {
    if (confirm('Are you sure you want to unban this customer?')) {
        fetch(`/admin/suspicious-customers/${customerId}/unban`, {
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

document.getElementById('banForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const customerId = document.getElementById('banCustomerId').value;
    const formData = new FormData(this);
    
    fetch(`/admin/suspicious-customers/${customerId}/ban`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeBanModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while banning the customer.');
    });
});
</script>
@endsection
