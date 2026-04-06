@extends('admin.layout')

@section('title', 'Courier Integrations')
@section('header', 'Courier Integrations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-shipping-fast mr-3"></i>Courier Integrations
                </h1>
                <p class="text-blue-100">Manage Steadfast and Pathao courier integrations</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="showBulkIntegrationModal()" style="background:rgba(255,255,255,0.2); color:white; padding:8px 16px; border-radius:8px; border:none; cursor:pointer; font-size:14px;">
                    <i class="fas fa-layer-group mr-2"></i>Bulk Integrate
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Total</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_integrations'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-orange-400 to-red-500 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-truck text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Steadfast</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['steadfast_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-bicycle text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Pathao</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['pathao_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-shipping-fast text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">eCourier</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['ecourier_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-red-400 to-red-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-box text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">RedX</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['redx_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-paper-plane text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Paperfly</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['paperfly_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-yellow-400 to-orange-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-sun text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Sundarban</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['sundarban_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-bus text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">SA Paribahan</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['saparibahan_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-pink-400 to-pink-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-female text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Janani</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['janani_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Synced</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['synced_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-gradient-to-br from-red-400 to-red-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Failed</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['failed_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Integrations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Integration History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tracking</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($integrations as $integration)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $integration->order->order_number }}</div>
                                <div class="text-sm text-gray-500">৳{{ number_format($integration->order->total_amount, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($integration->courier_type === 'steadfast')
                                        <div class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-truck mr-1"></i>Steadfast
                                        </div>
                                    @elseif($integration->courier_type === 'pathao')
                                        <div class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-bicycle mr-1"></i>Pathao
                                        </div>
                                    @elseif($integration->courier_type === 'ecourier')
                                        <div class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-shipping-fast mr-1"></i>eCourier
                                        </div>
                                    @elseif($integration->courier_type === 'redx')
                                        <div class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-box mr-1"></i>RedX
                                        </div>
                                    @elseif($integration->courier_type === 'paperfly')
                                        <div class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-paper-plane mr-1"></i>Paperfly
                                        </div>
                                    @elseif($integration->courier_type === 'sundarban')
                                        <div class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-sun mr-1"></i>Sundarban
                                        </div>
                                    @elseif($integration->courier_type === 'saparibahan')
                                        <div class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-bus mr-1"></i>SA Paribahan
                                        </div>
                                    @elseif($integration->courier_type === 'janani')
                                        <div class="bg-pink-100 text-pink-800 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-female mr-1"></i>Janani
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($integration->tracking_number)
                                    <div class="text-sm font-mono text-gray-900">{{ $integration->tracking_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $integration->consignment_id }}</div>
                                @else
                                    <span class="text-sm text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $integration->customer_name }}</div>
                                <div class="text-sm text-gray-500">{{ $integration->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $integration->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $integration->created_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.courier-integrations.show', $integration) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($integration->status === 'failed')
                                        <a href="{{ route('admin.courier-integrations.retry', $integration) }}" 
                                           class="text-green-600 hover:text-green-900" title="Retry">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    @endif
                                    @if($integration->status === 'synced')
                                        <form method="POST" action="{{ route('admin.courier-integrations.cancel', $integration) }}" 
                                              onsubmit="return confirm('Are you sure you want to cancel this integration?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-shipping-fast text-4xl text-gray-300 mb-3"></i>
                                    <p>No courier integrations found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $integrations->links() }}
        </div>
    </div>
</div>

<!-- Bulk Integration Modal -->
<div id="bulkIntegrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-layer-group mr-2 text-blue-500"></i>Bulk Courier Integration
                </h3>
                <button type="button" onclick="closeBulkIntegrationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="bulk-integration-form" method="POST" action="{{ route('admin.courier-integrations.bulk-integrate') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Courier Service</label>
                        <select class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="courier_type" required>
                            <option value="">Select Courier Service</option>
                            <option value="steadfast">Steadfast Courier</option>
                            <option value="pathao">Pathao Courier</option>
                            <option value="ecourier">eCourier</option>
                            <option value="redx">RedX Courier</option>
                            <option value="paperfly">Paperfly</option>
                            <option value="sundarban">Sundarban Courier</option>
                            <option value="saparibahan">SA Paribahan</option>
                            <option value="janani">Janani Express</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order IDs</label>
                        <textarea class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  name="order_ids" rows="4" placeholder="Enter order IDs separated by commas (e.g., 1,2,3,4,5)" required></textarea>
                        <p class="mt-1 text-sm text-gray-500">Enter the order IDs you want to integrate with the courier service.</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-sm text-blue-700">Only orders that haven't been integrated yet will be processed.</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeBulkIntegrationModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium shadow-lg">
                        <i class="fas fa-shipping-fast mr-2"></i>Integrate Orders
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showBulkIntegrationModal() {
    document.getElementById('bulkIntegrationModal').classList.remove('hidden');
}

function closeBulkIntegrationModal() {
    document.getElementById('bulkIntegrationModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('bulkIntegrationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkIntegrationModal();
    }
});

// Auto-refresh stats every 30 seconds
setInterval(() => {
    fetch('{{ route("admin.courier-integrations.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update stats if needed
            console.log('Stats updated:', data);
        })
        .catch(error => console.error('Error updating stats:', error));
}, 30000);
</script>
@endsection
