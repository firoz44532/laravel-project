<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Here you would typically:
        // 1. Send email to admin
        // 2. Store in database
        // 3. Send confirmation to user
        
        // For now, just return success message
        return redirect()
            ->route('contact')
            ->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }

    public function faq()
    {
        return view('frontend.pages.faq');
    }

    public function shipping()
    {
        return view('frontend.pages.shipping');
    }

    public function returns()
    {
        return view('frontend.pages.returns');
    }

    public function privacy()
    {
        return 'This is the Privacy Policy page.';
    }

    public function terms()
    {
        return 'This is the Terms & Conditions page.';
    }
}
