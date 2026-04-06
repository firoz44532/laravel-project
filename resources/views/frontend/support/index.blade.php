@extends('frontend.layout')

@section('title', 'Support Tickets')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Support Tickets</h1>
                    <p class="text-gray-600 mt-2">Manage your support requests and track their progress</p>
                </div>
                <a href="{{ route('support.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-plus mr-2"></i>New Ticket
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $tickets->total() }}</p>
                        <p class="text-sm text-gray-600">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $tickets->where('status', 'open')->count() }}</p>
                        <p class="text-sm text-gray-600">Open</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-spinner text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $tickets->where('status', 'in_progress')->count() }}</p>
                        <p class="text-sm text-gray-600">In Progress</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $tickets->where('status', 'resolved')->count() }}</p>
                        <p class="text-sm text-gray-600">Resolved</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Your Tickets</h2>
            </div>
            
            @if($tickets->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                        <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <a href="{{ route('support.show', $ticket->id) }}" class="text-lg font-medium text-gray-900 hover:text-blue-600 mr-3">
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
                                            <i class="fas fa-folder mr-1"></i>{{ ucfirst($ticket->category) }}
                                        </span>
                                        <span class="mr-4">
                                            <i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('M j, Y') }}
                                        </span>
                                        @if($ticket->latestMessage)
                                            <span>
                                                <i class="fas fa-comment mr-1"></i>{{ $ticket->latestMessage->created_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('support.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No support tickets yet</h3>
                    <p class="text-gray-600 mb-6">Create your first support ticket to get help from our team.</p>
                    <a href="{{ route('support.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                        Create Ticket
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
