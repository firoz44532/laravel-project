<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->with(['latestMessage', 'assignedTo'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('frontend.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('frontend.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:general,technical,billing,shipping,product,account',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string|min:10',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'status' => 'open',
        ]);

        // Create initial message
        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->description,
            'is_admin' => false,
        ]);

        return redirect()->route('support.show', $ticket->id)
            ->with('success', 'Support ticket created successfully!');
    }

    public function show($id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())
            ->with(['messages.user', 'assignedTo'])
            ->findOrFail($id);

        // Mark messages as read (if needed)
        $ticket->messages()->where('is_admin', true)->update(['read_at' => now()]);

        return view('frontend.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:1',
        ]);

        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);

        if ($ticket->isResolved()) {
            return back()->with('error', 'Cannot reply to resolved tickets.');
        }

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_admin' => false,
        ]);

        // Update ticket status if it was closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent successfully!');
    }

    public function close($id)
    {
        $ticket = SupportTicket::where('user_id', Auth::id())->findOrFail($id);
        
        if ($ticket->isOpen()) {
            $ticket->update([
                'status' => 'closed',
                'resolved_at' => now(),
            ]);
            
            return back()->with('success', 'Ticket closed successfully!');
        }

        return back()->with('error', 'Ticket is already closed.');
    }
}
