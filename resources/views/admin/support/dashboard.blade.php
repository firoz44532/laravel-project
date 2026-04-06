@extends('admin.layout')

@section('title', 'Support Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Support Dashboard</h1>
            <p class="text-gray-600 mt-2">Monitor and manage all support tickets</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
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
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['open'] }}</p>
                        <p class="text-sm text-gray-600">Open Tickets</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
                        <p class="text-sm text-gray-600">Resolved</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['urgent'] }}</p>
                        <p class="text-sm text-gray-600">Urgent</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Tickets -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Tickets</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($recentTickets as $ticket)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                        {{ $ticket->subject }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $ticket->user->name }} • {{ $ticket->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($ticket->status == 'open') bg-yellow-100 text-yellow-800
                                    @elseif($ticket->status == 'in_progress') bg-purple-100 text-purple-800
                                    @elseif($ticket->status == 'resolved') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <a href="{{ route('admin.support.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        View all tickets <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Category Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Tickets by Category</h2>
                </div>
                <div class="p-6">
                    @foreach($categoryStats as $stat)
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($stat->category) }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($stat->count / $stats['total']) * 100 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $stat->count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
