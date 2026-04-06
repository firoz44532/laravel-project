@extends('admin.layout')

@section('title', 'Support Tickets')

@section('header', 'Support Tickets')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.support.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search tickets..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
                
                <!-- Priority Filter -->
                <div>
                    <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>All Priority</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Category</option>
                        <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>Billing</option>
                        <option value="shipping" {{ request('category') == 'shipping' ? 'selected' : '' }}>Shipping</option>
                        <option value="product" {{ request('category') == 'product' ? 'selected' : '' }}>Product</option>
                        <option value="account" {{ request('category') == 'account' ? 'selected' : '' }}>Account</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tickets List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">All Tickets</h2>
                <div class="text-sm text-gray-600">
                    {{ $tickets->total() }} tickets found
                </div>
            </div>
        </div>
        
        @if($tickets->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($tickets as $ticket)
                    <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-lg font-medium text-gray-900 hover:text-blue-600 mr-3">
                                        {{ $ticket->subject }}
                                    </a>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ticket->status == 'open') bg-yellow-100 text-yellow-800
                                        @elseif($ticket->status == 'in_progress') bg-purple-100 text-purple-800
                                        @elseif($ticket->status == 'resolved') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($ticket->priority == 'urgent') bg-red-100 text-red-800
                                        @elseif($ticket->priority == 'high') bg-orange-100 text-orange-800
                                        @elseif($ticket->priority == 'medium') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif ml-2">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </div>
                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                    <span class="mr-4">
                                        <i class="fas fa-user mr-1"></i>{{ $ticket->user->name }}
                                    </span>
                                    <span class="mr-4">
                                        <i class="fas fa-folder mr-1"></i>{{ ucfirst($ticket->category) }}
                                    </span>
                                    <span class="mr-4">
                                        <i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('M j, Y') }}
                                    </span>
                                    @if($ticket->assignedTo)
                                        <span class="mr-4">
                                            <i class="fas fa-user-check mr-1"></i>{{ $ticket->assignedTo->name }}
                                        </span>
                                    @endif
                                    @if($ticket->latestMessage)
                                        <span>
                                            <i class="fas fa-comment mr-1"></i>{{ $ticket->latestMessage->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                    View <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tickets->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No tickets found</h3>
                <p class="text-gray-600">No tickets match your current filters.</p>
            </div>
        @endif
    </div>
</div>
@endsection
