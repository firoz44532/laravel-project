<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        return view('frontend.contact.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $contactMessage = ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'pending',
            'user_id' => Auth::check() ? Auth::id() : null,
        ]);

        return redirect()
            ->route('contact.index')
            ->with('success', 'Your message has been sent successfully! We will get back to you soon.');
    }

    public function support()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to access support center');
        }

        $tickets = Ticket::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('frontend.contact.support', compact('tickets'));
    }

    public function createTicket()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to create a support ticket');
        }

        return view('frontend.contact.create-ticket');
    }

    public function storeTicket(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to create a support ticket'
            ], 401);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:general,technical,billing,shipping,product,other',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string|max:2000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-' . strtoupper(Str::random(8)),
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'message' => $request->message,
            'status' => 'open',
            'order_id' => $request->order_id,
        ]);

        return redirect()
            ->route('contact.support')
            ->with('success', 'Support ticket created successfully! Ticket #' . $ticket->ticket_number);
    }

    public function showTicket($ticketNumber)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to view support tickets');
        }

        $ticket = Ticket::where('ticket_number', $ticketNumber)
            ->where('user_id', Auth::id())
            ->with(['replies' => function($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->firstOrFail();

        return view('frontend.contact.ticket', compact('ticket'));
    }

    public function replyTicket(Request $request, $ticketNumber)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to reply to tickets'
            ], 401);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $ticket = Ticket::where('ticket_number', $ticketNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $ticket->replies()->create([
            'message' => $request->message,
            'is_admin' => false,
            'user_id' => Auth::id(),
        ]);

        $ticket->update(['status' => 'pending']);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully!'
        ]);
    }

    public function closeTicket($ticketNumber)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to manage tickets');
        }

        $ticket = Ticket::where('ticket_number', $ticketNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $ticket->update(['status' => 'closed']);

        return redirect()
            ->route('contact.support')
            ->with('success', 'Ticket closed successfully!');
    }

    public function faq()
    {
        $faqs = [
            [
                'question' => 'How do I place an order?',
                'answer' => 'Browse our products, add items to your cart, proceed to checkout, and complete your order with your preferred payment method.',
                'category' => 'general'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept bKash, Nagad, Rocket, Upay, credit/debit cards, and cash on delivery.',
                'category' => 'billing'
            ],
            [
                'question' => 'How long does delivery take?',
                'answer' => 'Standard delivery takes 3-5 business days within Bangladesh. Express delivery takes 1-2 business days.',
                'category' => 'shipping'
            ],
            [
                'question' => 'Can I track my order?',
                'answer' => 'Yes! You can track your order using the order number on our tracking page.',
                'category' => 'shipping'
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 7-day return policy for unused items in original packaging. Please contact our support team for assistance.',
                'category' => 'general'
            ],
            [
                'question' => 'How do I contact customer support?',
                'answer' => 'You can reach us through our contact form, support ticket system, or call us at +880 1234 567890.',
                'category' => 'general'
            ],
            [
                'question' => 'Do you offer international shipping?',
                'answer' => 'Currently, we only ship within Bangladesh. We plan to expand internationally soon.',
                'category' => 'shipping'
            ],
            [
                'question' => 'How can I cancel my order?',
                'answer' => 'You can cancel your order within 24 hours of placing it. Contact our support team for assistance.',
                'category' => 'general'
            ],
        ];

        return view('frontend.contact.faq', compact('faqs'));
    }
}
