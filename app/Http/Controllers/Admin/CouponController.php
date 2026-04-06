<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Str;
use App\Http\Requests\Admin\CouponStoreRequest;
use App\Http\Requests\Admin\CouponUpdateRequest;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(CouponStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');
        $validated['used_count'] = 0;

        Coupon::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    public function show(Coupon $coupon)
    {
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(CouponUpdateRequest $request, Coupon $coupon)
    {
        $validated = $request->validated();

        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon status updated successfully!',
            'is_active' => $coupon->is_active
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        // Check if coupon is expired
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon has expired'
            ]);
        }

        // Check if coupon has started
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon is not yet active'
            ]);
        }

        // Check minimum amount
        if ($coupon->minimum_amount && $request->total_amount < $coupon->minimum_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Minimum order amount is ৳{$coupon->minimum_amount}"
            ]);
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon usage limit reached'
            ]);
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->type === 'fixed') {
            $discount = min($coupon->value, $request->total_amount);
        } else {
            $discount = ($request->total_amount * $coupon->value) / 100;
        }

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!'
        ]);
    }
}
