<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use App\Http\Requests\Admin\SupportReplyRequest;
use App\Http\Requests\Admin\SupportAssignRequest;
use App\Http\Requests\Admin\SupportStatusUpdateRequest;
use App\Http\Requests\Admin\SupportPriorityUpdateRequest;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'latestMessage'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('updated_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'open') {
                $query->open();
            } elseif ($request->status === 'resolved') {
                $query->resolved();
            }
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20);
        $staff = User::where('role', 'admin')->orWhere('role', 'staff')->get();

        return view('admin.support.index', compact('tickets', 'staff'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['messages.user', 'user', 'assignedTo'])
            ->findOrFail($id);

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(SupportReplyRequest $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_admin' => true,
        ]);

        // Update ticket status
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent successfully!');
    }

    public function assign(SupportAssignRequest $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Ticket assigned successfully!');
    }

    public function updateStatus(SupportStatusUpdateRequest $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $ticket->update($updateData);

        return back()->with('success', 'Ticket status updated successfully!');
    }

    public function updatePriority(SupportPriorityUpdateRequest $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['priority' => $request->priority]);

        return back()->with('success', 'Priority updated successfully!');
    }

    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.support.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    public function dashboard()
    {
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::open()->count(),
            'resolved' => SupportTicket::resolved()->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->count(),
        ];

        $recentTickets = SupportTicket::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $categoryStats = SupportTicket::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.support.dashboard', compact('stats', 'recentTickets', 'categoryStats'));
    }
}
