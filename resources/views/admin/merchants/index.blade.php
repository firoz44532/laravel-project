@extends('admin.layout')

@section('title', 'Merchant Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Daraz-style Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-1 flex items-center">
                        <i class="fas fa-store mr-3"></i>
                        Merchant Management
                    </h1>
                    <p class="text-orange-100 text-sm">Manage and monitor all merchant accounts efficiently</p>
                </div>
                <div class="flex space-x-3">
                    @if($stats['pending_merchants'] > 0)
                    <div class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-lg font-semibold animate-pulse">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ $stats['pending_merchants'] }} Pending Approval
                    </div>
                    @endif
                    <div class="relative">
                        <button onclick="toggleExportMenu()" class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md flex items-center">
                            <i class="fas fa-download mr-2"></i>Export Report
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="exportMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-10">
                            <a href="/merchants/export?format=csv" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                <i class="fas fa-file-csv mr-2 text-green-600"></i>
                                Export as CSV
                            </a>
                            <a href="/merchants/export?format=excel" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                <i class="fas fa-file-excel mr-2 text-green-700"></i>
                                Export as Excel
                            </a>
                            <a href="/merchants/export?format=pdf" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                Export as PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Merchants -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Merchants</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_merchants'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-green-600 font-medium">+{{ $stats['monthly_new_merchants'] }} this month</span>
                        </div>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="fas fa-store text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Approved Merchants -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Merchants</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['approved_merchants'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">{{ $stats['total_merchants'] > 0 ? round(($stats['approved_merchants'] / $stats['total_merchants']) * 100, 1) : 0 }}% approval rate</span>
                        </div>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_products'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">{{ $stats['total_merchants'] > 0 ? round($stats['total_products'] / $stats['total_merchants'], 1) : 0 }} avg per merchant</span>
                        </div>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($stats['total_revenue'], 0) }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">All time sales</span>
                        </div>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Pending</p>
                        <p class="text-xl font-bold text-yellow-900">{{ $stats['pending_merchants'] }}</p>
                    </div>
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800">Approved</p>
                        <p class="text-xl font-bold text-green-900">{{ $stats['approved_merchants'] }}</p>
                    </div>
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-800">Rejected</p>
                        <p class="text-xl font-bold text-red-900">{{ $stats['rejected_merchants'] }}</p>
                    </div>
                    <i class="fas fa-times text-red-600"></i>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Suspended</p>
                        <p class="text-xl font-bold text-gray-900">{{ $stats['suspended_merchants'] }}</p>
                    </div>
                    <i class="fas fa-pause text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Merchants Table -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">All Merchants</h2>
                            <div class="flex items-center space-x-2">
                                <input type="text" placeholder="Search merchants..." class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        @if($merchants->count() > 0)
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach($merchants as $merchant)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($merchant->logo_url)
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $merchant->logo_url }}" alt="{{ $merchant->store_name }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                                <i class="fas fa-store text-gray-400"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $merchant->store_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $merchant->user->name }}</div>
                                                        <div class="text-xs text-gray-400">{{ $merchant->created_at->format('M d, Y') }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $merchant->products_count }}</div>
                                                <div class="text-xs text-gray-500">{{ $merchant->products->where('is_active', true)->count() }} active</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $merchant->orders_count }}</div>
                                                <div class="text-xs text-gray-500">completed</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">৳{{ number_format($merchant->total_revenue, 0) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $merchant->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                                       ($merchant->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                       ($merchant->status === 'suspended' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800')) }}">
                                                    {{ ucfirst($merchant->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('admin.merchants.show', $merchant) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($merchant->status === 'pending')
                                                        <form method="POST" action="{{ route('admin.merchants.approve', $merchant) }}" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <button type="button" class="text-red-600 hover:text-red-900" title="Reject" 
                                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $merchant->id }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @elseif($merchant->status === 'approved')
                                                        <form method="POST" action="{{ route('admin.merchants.suspend', $merchant) }}" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Suspend">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($merchant->status === 'suspended')
                                                        <form method="POST" action="{{ route('admin.merchants.approve', $merchant) }}" class="inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Reactivate">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-700">
                                        Showing {{ $merchants->firstItem() }} to {{ $merchants->lastItem() }} of {{ $merchants->total() }} results
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        {{ $merchants->links() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-store text-gray-300 text-5xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No merchants yet</h3>
                                <p class="text-gray-500">Merchant applications will appear here for your review.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Recent Applications -->
                @if($recentApplications->count() > 0)
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($recentApplications as $application)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-yellow-100 rounded-full p-2">
                                            <i class="fas fa-store text-yellow-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $application->store_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $application->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <form method="POST" action="{{ route('admin.merchants.approve', $application) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="text-red-600 hover:text-red-900" title="Reject" 
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $application->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Top Performing Merchants -->
                @if($topMerchants->count() > 0)
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Top Performers</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($topMerchants as $index => $merchant)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-gray-100 rounded-lg w-8 h-8 flex items-center justify-center font-semibold text-gray-600 text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $merchant->store_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $merchant->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 text-sm">{{ $merchant->orders_count }}</p>
                                        <p class="text-xs text-gray-500">orders</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Approval Rate</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_merchants'] > 0 ? round(($stats['approved_merchants'] / $stats['total_merchants']) * 100, 1) : 0 }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Avg Products/Merchant</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_merchants'] > 0 ? round($stats['total_products'] / $stats['total_merchants'], 1) : 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Monthly Growth</span>
                            <span class="font-semibold text-green-600">+{{ $stats['monthly_new_merchants'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modals -->
@foreach($merchants->where('status', 'pending') as $merchant)
<div class="modal fade" id="rejectModal{{ $merchant->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Merchant Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.merchants.reject', $merchant) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason_{{ $merchant->id }}" class="form-label">Rejection Reason *</label>
                        <textarea class="form-control" id="rejection_reason_{{ $merchant->id }}" name="rejection_reason" rows="4" required></textarea>
                        <small class="form-text text-muted">This reason will be sent to the merchant.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add search functionality
    const searchInput = document.querySelector('input[placeholder="Search merchants..."]');
    const statusSelect = document.querySelector('select');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    if (statusSelect) {
        statusSelect.addEventListener('change', function(e) {
            const status = e.target.value;
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!status) {
                    row.style.display = '';
                } else {
                    const statusBadge = row.querySelector('td:nth-child(5) span');
                    const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase() : '';
                    row.style.display = rowStatus.includes(status) ? '' : 'none';
                }
            });
        });
    }

    // Close export menu when clicking outside
    document.addEventListener('click', function(event) {
        const exportMenu = document.getElementById('exportMenu');
        const exportButton = event.target.closest('button[onclick="toggleExportMenu()"]');
        
        if (!exportButton && !exportMenu.contains(event.target)) {
            exportMenu.classList.add('hidden');
        }
    });
});

function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
}

function exportMerchants(format) {
    // Hide the menu
    document.getElementById('exportMenu').classList.add('hidden');
    
    // Show loading indicator
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
    button.disabled = true;
    
    // Get current filter values
    const searchValue = document.querySelector('input[placeholder="Search merchants..."]')?.value || '';
    const statusValue = document.querySelector('select')?.value || '';
    
    // Build URL with parameters
    const params = new URLSearchParams({
        format: format,
        search: searchValue,
        status: statusValue
    });
    
    console.log('Exporting with params:', params.toString());
    
    // Make AJAX request to export endpoint
    fetch(`/admin/merchants/export?${params}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'text/csv,application/vnd.ms-excel,application/pdf,text/html,*/*'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (response.ok) {
                return response.blob();
            }
            return response.text().then(text => {
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        })
        .then(blob => {
            console.log('Blob size:', blob.size);
            console.log('Blob type:', blob.type);
            
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Set filename based on format
            const timestamp = new Date().toISOString().slice(0, 10);
            a.download = `merchants_export_${timestamp}.${format}`;
            
            // Trigger download
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            // Show success message
            showNotification('Export completed successfully!', 'success');
        })
        .catch(error => {
            console.error('Export error:', error);
            showNotification('Export failed: ' + error.message, 'error');
        })
        .finally(() => {
            // Restore button
            button.innerHTML = originalContent;
            button.disabled = false;
        });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            ${message}
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
