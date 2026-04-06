@extends('admin.layout')

@section('title', 'Support Ticket - ' . $ticket->subject)

@section('header', 'Support Ticket: ' . $ticket->subject)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Ticket Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="flex flex-wrap items-center gap-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($ticket->status == 'open') bg-yellow-100 text-yellow-800
                    @elseif($ticket->status == 'in_progress') bg-purple-100 text-purple-800
                    @elseif($ticket->status == 'resolved') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($ticket->priority == 'urgent') bg-red-100 text-red-800
                    @elseif($ticket->priority == 'high') bg-orange-100 text-orange-800
                    @elseif($ticket->priority == 'medium') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($ticket->priority) }} Priority
                </span>
                <span class="text-sm text-gray-600">
                    <i class="fas fa-folder mr-1"></i>{{ ucfirst($ticket->category) }}
                </span>
                <span class="text-sm text-gray-600">
                    <i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('M j, Y g:i A') }}
                </span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Created by</p>
                <p class="font-medium">{{ $ticket->user->name }} ({{ $ticket->user->email }})</p>
            </div>
            @if($ticket->assignedTo)
                <div>
                    <p class="text-sm text-gray-600">Assigned to</p>
                    <p class="font-medium">{{ $ticket->assignedTo->name }}</p>
                </div>
            @endif
        </div>
        
        @if($ticket->resolved_at)
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3">
                <p class="text-sm text-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    This ticket was resolved on {{ $ticket->resolved_at->format('M j, Y g:i A') }}
                </p>
            </div>
        @endif
    </div>

    <!-- Messages -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Conversation</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($ticket->messages as $message)
                <div class="px-6 py-4 {{ $message->is_admin ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @if($message->is_admin)
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-headset text-white"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900">
                                        {{ $message->is_admin ? 'Support Team' : $message->user->name }}
                                    </span>
                                    @if($message->is_admin)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Staff
                                        </span>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $message->created_at->format('M j, Y g:i A') }}
                                </span>
                            </div>
                            <div class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Reply Form -->
    @if($ticket->isOpen())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Reply to Ticket</h2>
            </div>
            <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}" class="p-6">
                @csrf
                <div class="mb-4">
                    <textarea name="message" 
                              required
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Type your reply here..."></textarea>
                </div>
                <div class="flex justify-between">
                    <div class="space-x-3">
                        <!-- Status Update -->
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        
                        <!-- Priority Update -->
                        <select name="priority" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        
                        <!-- Assignment -->
                        <select name="assigned_to" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Unassigned</option>
                            @foreach($staff as $staffMember)
                                <option value="{{ $staffMember->id }}" {{ $ticket->assigned_to == $staffMember->id ? 'selected' : '' }}>
                                    {{ $staffMember->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="space-x-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>Send Reply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-100 rounded-lg p-6 text-center">
            <i class="fas fa-lock text-gray-400 text-3xl mb-3"></i>
            <p class="text-gray-600">This ticket is closed. No further replies can be added.</p>
        </div>
    @endif
</div>
@endsection
