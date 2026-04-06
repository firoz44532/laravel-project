<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterConfirmation;
use App\Mail\NewsletterUnsubscribe;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $newsletter = Newsletter::subscribe(
            $request->email,
            $request->first_name,
            $request->last_name
        );

        // Send confirmation email
        try {
            Mail::to($newsletter->email)->send(new NewsletterConfirmation($newsletter));
        } catch (\Exception $e) {
            // Log error but don't fail the subscription
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing to our newsletter!',
            'newsletter' => $newsletter
        ]);
    }

    public function unsubscribe($token = null)
    {
        if (!$token) {
            return view('frontend.newsletter.unsubscribe');
        }

        $newsletter = Newsletter::where('unsubscribe_token', $token)->first();

        if (!$newsletter) {
            return redirect()->route('home')->with('error', 'Invalid unsubscribe link');
        }

        if ($newsletter->unsubscribe($token)) {
            // Send confirmation email
            try {
                Mail::to($newsletter->email)->send(new NewsletterUnsubscribe($newsletter));
            } catch (\Exception $e) {
                // Log error but don't fail the unsubscribe
            }

            return view('frontend.newsletter.unsubscribed')->with('success', 'You have been successfully unsubscribed from our newsletter.');
        }

        return redirect()->route('home')->with('error', 'Unable to process unsubscribe request. Please contact support.');
    }

    public function manage()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to manage newsletter preferences');
        }

        $userEmail = Auth::user()->email;
        $newsletter = Newsletter::where('email', $userEmail)->first();

        return view('frontend.newsletter.manage', compact('newsletter'));
    }

    public function updatePreferences(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update preferences'
            ], 401);
        }

        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        $userEmail = Auth::user()->email;
        $newsletter = Newsletter::where('email', $userEmail)->first();

        if ($newsletter) {
            $newsletter->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);
        } else {
            Newsletter::create([
                'email' => $userEmail,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'is_active' => true,
                'unsubscribe_token' => Str::random(40),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Newsletter preferences updated successfully!'
        ]);
    }

    public function unsubscribeUser()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to unsubscribe'
            ], 401);
        }

        $userEmail = Auth::user()->email;
        $newsletter = Newsletter::where('email', $userEmail)->first();

        if ($newsletter) {
            $newsletter->unsubscribe();

            // Send confirmation email
            try {
                Mail::to($newsletter->email)->send(new NewsletterUnsubscribe($newsletter));
            } catch (\Exception $e) {
                // Log error but don't fail the unsubscribe
            }

            return response()->json([
                'success' => true,
                'message' => 'You have been unsubscribed from our newsletter.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No active subscription found.'
        ]);
    }

    public function getSubscriberCount()
    {
        $count = Newsletter::active()->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function adminIndex()
    {
        $newsletters = Newsletter::latest()->paginate(20);
        
        return view('admin.newsletters.index', compact('newsletters'));
    }

    public function adminExport()
    {
        $newsletters = Newsletter::latest()->get();
        
        $csvContent = "Email,First Name,Last Name,Status,Subscribed At,Unsubscribed At\n";
        
        foreach ($newsletters as $newsletter) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $newsletter->email,
                $newsletter->first_name ?? '',
                $newsletter->last_name ?? '',
                $newsletter->is_active ? 'Active' : 'Inactive',
                $newsletter->formatted_subscribed_at,
                $newsletter->formatted_unsubscribed_at ?? ''
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="subscribers.csv"');
    }

    public function adminSendEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipients' => 'required|array',
            'recipients.*' => 'email',
        ]);

        $subscribers = Newsletter::active()
            ->whereIn('email', $request->recipients)
            ->get();

        foreach ($subscribers as $subscriber) {
            try {
                Mail::to($subscriber->email)->send(new \App\Mail\NewsletterCampaign($request->subject, $request->message, $subscriber));
            } catch (\Exception $e) {
                // Log error but continue with other subscribers
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Newsletter sent to ' . $subscribers->count() . ' subscribers'
        ]);
    }
}
