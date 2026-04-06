<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;

class UserController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Attach any pending guest address to this account
            $pending = $request->session()->pull('pending_save_address_id');
            if ($pending) {
                $addr = Address::find($pending);
                if ($addr && !$addr->user_id) {
                    $addr->user_id = Auth::id();
                    $addr->is_default = true;
                    $addr->save();
                    $request->session()->flash('success', 'We saved your shipping address to your account.');
                }
            }

            if ($request->user()->is_active) {
                return redirect()->intended(route('home'));
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Check for fake customer detection results from middleware
        $detection = $request->get('fake_customer_detection');
        
        if ($detection && $detection['should_review']) {
            // Mark user for manual review but allow registration
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone' => $validated['phone'],
                'is_active' => false, // Require manual activation
                'role' => 'customer',
                'suspicious_flags' => $detection['flags'],
                'risk_score' => $detection['risk_score'],
                'risk_level' => $detection['risk_level'],
                'flagged_at' => now(),
            ]);

            // Log for admin review
            \Log::info('User flagged for manual review', [
                'user_id' => $user->id,
                'email' => $user->email,
                'risk_score' => $detection['risk_score'],
                'flags' => $detection['flags'],
            ]);

            Auth::login($user);

            return redirect()->route('home')->with('warning', 'Registration successful! Your account is under review and will be activated shortly.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'],
            'is_active' => true,
            'role' => 'customer',
        ]);

        // Save detection data if available (even for low risk)
        if ($detection && $detection['risk_score'] > 0) {
            $user->update([
                'suspicious_flags' => $detection['flags'],
                'risk_score' => $detection['risk_score'],
                'risk_level' => $detection['risk_level'],
            ]);
        }

        Auth::login($user);

        // Attach any pending guest address after registration
        $pending = $request->session()->pull('pending_save_address_id');
        if ($pending) {
            $addr = Address::find($pending);
            if ($addr && !$addr->user_id) {
                $addr->user_id = $user->id;
                $addr->is_default = true;
                $addr->save();
                $request->session()->flash('success', 'We saved your shipping address to your account.');
            }
        }

        return redirect()->route('home')->with('success', 'Registration successful!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        $addresses = Address::where('user_id', $user->id)->get();
        
        return view('frontend.account.dashboard', compact('user', 'orders', 'addresses'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function addresses()
    {
        $addresses = Auth::user()->addresses;
        return view('frontend.account.addresses', compact('addresses'));
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);
        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        $order->load(['items', 'shippingAddress', 'billingAddress', 'payment']);
        return view('frontend.account.order-detail', compact('order'));
    }
}
