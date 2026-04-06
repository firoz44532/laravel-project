<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        
        return view('frontend.account.addresses', compact('addresses'));
    }

    public function create()
    {
        $from = request('from');
        return view('frontend.account.addresses-create', compact('from'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'division' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_default'] = $request->has('is_default');

        // If this is set as default, unset other default addresses of same type
        if ($validated['is_default']) {
            Address::where('user_id', Auth::id())
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        // If user has no other addresses of this type, make this default
        $existingCount = Address::where('user_id', Auth::id())
            ->where('type', $validated['type'])
            ->count();

        if ($existingCount === 0) {
            $validated['is_default'] = true;
        }

        $address = Address::create($validated);

        // If form came from checkout, redirect back to checkout with welcome message
        if ($request->input('from') === 'checkout') {
            return redirect()->route('checkout.index')
                ->with('success', 'Welcome! Your shipping address has been saved. You can now place your order.')
                ->with('new_address_id', $address->id);
        }

        return redirect()
            ->route('account.addresses')
            ->with('success', 'Address added successfully!');
    }

    public function edit(Address $address)
    {
        // Check if address belongs to authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.account.addresses-edit', compact('address'));
    }

    public function update(Request $request, Address $address)
    {
        // Check if address belongs to authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'division' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'is_default' => 'boolean',
        ]);

        $validated['is_default'] = $request->has('is_default');

        // If this is set as default, unset other default addresses of same type
        if ($validated['is_default']) {
            Address::where('user_id', Auth::id())
                ->where('type', $validated['type'])
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()
            ->route('account.addresses')
            ->with('success', 'Address updated successfully!');
    }

    public function destroy(Address $address)
    {
        // Check if address belongs to authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()
            ->route('account.addresses')
            ->with('success', 'Address deleted successfully!');
    }

    public function setDefault(Address $address)
    {
        // Check if address belongs to authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Unset other default addresses of same type
        Address::where('user_id', Auth::id())
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        // Set this as default
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated successfully!'
        ]);
    }

    // Get divisions of Bangladesh
    public function getDivisions()
    {
        return [
            'Barishal' => 'Barishal',
            'Chattogram' => 'Chattogram',
            'Dhaka' => 'Dhaka',
            'Khulna' => 'Khulna',
            'Mymensingh' => 'Mymensingh',
            'Rajshahi' => 'Rajshahi',
            'Rangpur' => 'Rangpur',
            'Sylhet' => 'Sylhet',
        ];
    }

    // Get cities for a division (simplified)
    public function getCities($division)
    {
        $cities = [
            'Dhaka' => ['Dhaka', 'Gazipur', 'Narayanganj', 'Manikganj', 'Munshiganj', 'Narsingdi', 'Faridpur', 'Gopalganj', 'Kishoreganj', 'Madaripur', 'Rajbari', 'Shariatpur', 'Tangail'],
            'Chattogram' => ['Chattogram', 'Cox\'s Bazar', 'Bandarban', 'Rangamati', 'Khagrachari', 'Feni', 'Noakhali', 'Lakshmipur', 'Comilla', 'Brahmanbaria', 'Chandpur'],
            'Rajshahi' => ['Rajshahi', 'Sirajganj', 'Pabna', 'Bogura', 'Naogaon', 'Joypurhat', 'Chapainawabganj', 'Natore', 'Kushtia', 'Meherpur'],
            'Khulna' => ['Khulna', 'Bagerhat', 'Satkhira', 'Jashore', 'Magura', 'Jhenaidah', 'Narail', 'Chuadanga', 'Meherpur', 'Kushtia'],
            'Barishal' => ['Barishal', 'Patuakhali', 'Bhola', 'Pirojpur', 'Jhalokathi', 'Barguna'],
            'Sylhet' => ['Sylhet', 'Moulvibazar', 'Habiganj', 'Sunamganj'],
            'Mymensingh' => ['Mymensingh', 'Jamalpur', 'Netrokona', 'Sherpur'],
            'Rangpur' => ['Rangpur', 'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Panchagarh', 'Thakurgaon'],
        ];

        return $cities[$division] ?? [];
    }
}
