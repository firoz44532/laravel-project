@extends('admin.layout')

@section('title', 'Orders')
@section('header', 'Orders Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Orders</h1>
    <div class="flex space-x-4">
        <div class="relative">
            <button onclick="toggleExportMenu()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 shadow-md flex items-center">
                <i class="fas fa-download mr-2"></i>Export Orders
                <i class="fas fa-chevron-down ml-2"></i>
            </button>
            <div id="exportMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-10">
                <a href="/orders/export?format=csv" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-file-csv mr-2 text-green-600"></i>
                    Export as CSV
                </a>
                <a href="/orders/export?format=excel" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-file-excel mr-2 text-green-700"></i>
                    Export as Excel
                </a>
                <a href="/orders/export?format=pdf" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                    Export as PDF
                </a>
            </div>
        </div>
        <button onclick="showBulkActions()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-tasks mr-2"></i>Bulk Actions
        </button>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search orders..." 
                   value="{{ request('search') }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
        </div>
        <select name="status" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" 
               class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
        <input type="date" name="date_to" value="{{ request('date_to') }}" 
               class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-times mr-2"></i>Clear
        </a>
    </form>
</div>

<!-- Bulk Actions (Hidden by default) -->
<div id="bulk-actions" class="bg-white rounded-lg shadow mb-6 p-4 hidden">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <input type="checkbox" id="select-all" class="mr-2">
            <label for="select-all" class="text-sm font-medium">Select All</label>
            <span id="selected-count" class="text-sm text-gray-600">0 selected</span>
        </div>
        <div class="flex space-x-2">
            <select id="bulk-status" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary text-sm">
                <option value="">Update Status</option>
                <option value="paid">Mark as Paid</option>
                <option value="processing">Mark as Processing</option>
                <option value="shipped">Mark as Shipped</option>
                <option value="delivered">Mark as Delivered</option>
                <option value="cancelled">Cancel Orders</option>
            </select>
            <button onclick="applyBulkAction()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                Apply
            </button>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="bulk-select" class="mr-2">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order #
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                            <div class="text-sm text-gray-500">{{ $order->items_count }} items</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold">{{ $order->formatted_total }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('M j, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}')" 
                                        class="text-green-600 hover:text-green-900" title="Update Status">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="window.open('{{ route('admin.orders.print', $order) }}', '_blank')" 
                                        class="text-purple-600 hover:text-purple-900" title="Print Invoice">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button onclick="sendInvoiceEmail({{ $order->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900" title="Email Invoice">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $orders->links() }}
    </div>
</div>

<!-- Status Update Modal -->
<div id="status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Update Order Status</h3>
            <form id="status-form">
                @csrf
                <input type="hidden" id="order-id" name="order_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                    <select name="status" id="status-select" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStatusModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-orange-600">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showBulkActions() {
    document.getElementById('bulk-actions').classList.remove('hidden');
}

function updateOrderStatus(orderId, currentStatus) {
    document.getElementById('order-id').value = orderId;
    document.getElementById('status-select').value = currentStatus;
    document.getElementById('status-modal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('status-modal').classList.add('hidden');
}

// Status form submission
document.getElementById('status-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const orderId = formData.get('order_id');
    
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: formData.get('status'),
            notes: formData.get('notes')
        })
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
        alert('Error updating order status');
    });
});

// Bulk actions
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.order-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checked = document.querySelectorAll('.order-checkbox:checked');
    document.getElementById('selected-count').textContent = checked.length + ' selected';
}

function applyBulkAction() {
    const checked = document.querySelectorAll('.order-checkbox:checked');
    const orderIds = Array.from(checked).map(cb => cb.value);
    const status = document.getElementById('bulk-status').value;
    
    if (orderIds.length === 0 || !status) {
        alert('Please select orders and a status');
        return;
    }
    
    fetch('/admin/orders/bulk-update-status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            order_ids: orderIds,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if (data.errors.length > 0) {
                alert('Errors:\n' + data.errors.join('\n'));
            }
            location.reload();
        } else {
            alert('Error updating orders');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating orders');
    });
}

function exportOrders() {
    const url = new URL(window.location.href);
    url.searchParams.set('export', '1');
    window.open(url, '_blank');
}

function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    const exportMenu = document.getElementById('exportMenu');
    const exportButton = event.target.closest('button[onclick="toggleExportMenu()"]');
    
    if (!exportButton && !exportMenu.contains(event.target)) {
        exportMenu.classList.add('hidden');
    }
});

function printInvoice(orderId) {
    window.open('/admin/orders/' + orderId + '/print', '_blank');
}

function sendInvoiceEmail(orderId) {
    fetch(`/admin/orders/${orderId}/send-invoice`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Invoice sent successfully!');
            if (window.VoiceNotifications) {
                window.VoiceNotifications.speak('Invoice sent successfully to customer');
            }
        } else {
            alert('Error sending invoice');
            if (window.VoiceNotifications) {
                window.VoiceNotifications.error('Failed to send invoice');
            }
        }
    });
}

// Voice notification for order status updates
function updateOrderStatus(orderId, status) {
    if (window.VoiceNotifications) {
        const orderNumber = document.querySelector(`[data-order-id="${orderId}"] [data-order-number]`)?.textContent || orderId;
        window.VoiceNotifications.orderStatusUpdate(status, orderNumber);
    }
}

// Periodic check for new orders (every 30 seconds)
setInterval(() => {
    if (window.VoiceNotifications && document.visibilityState === 'visible') {
        // Check for new orders (this would need an endpoint)
        // For now, just check if page has focus
        console.log('Checking for new orders...');
    }
}, 30000);

// Add voice control panel button
document.addEventListener('DOMContentLoaded', function() {
    if (window.VoiceNotifications) {
        // Add voice control button to admin header
        const header = document.querySelector('h1');
        if (header) {
            const voiceButton = document.createElement('button');
            voiceButton.className = 'ml-4 bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition';
            voiceButton.innerHTML = '<i class="fas fa-microphone mr-1"></i> Voice Controls';
            voiceButton.onclick = () => window.VoiceNotifications.showControlPanel();
            header.parentElement.appendChild(voiceButton);
        }
    }
});
</script>
@endpush
