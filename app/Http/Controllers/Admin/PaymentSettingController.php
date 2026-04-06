<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $paymentSettings = PaymentSetting::orderBy('sort_order')->get();
        return view('admin.payment-settings.index', compact('paymentSettings'));
    }

    public function edit($gateway)
    {
        $paymentSetting = PaymentSetting::where('gateway', $gateway)->firstOrFail();
        return view('admin.payment-settings.edit', compact('paymentSetting'));
    }

    public function update(Request $request, $gateway)
    {
        $paymentSetting = PaymentSetting::where('gateway', $gateway)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'instructions' => 'nullable|string',
            'sort_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update basic fields
        $paymentSetting->update([
            'display_name' => $request->display_name,
            'is_active' => $request->boolean('is_active'),
            'instructions' => $request->instructions,
            'sort_order' => $request->sort_order,
        ]);

        // Update gateway-specific settings
        $settings = $paymentSetting->settings ?? [];

        switch ($gateway) {
            case 'bkash':
            case 'nagad':
            case 'rocket':
                $settings['merchant_number'] = $request->merchant_number;
                $settings['account_name'] = $request->account_name;
                $settings['transaction_fee'] = $request->transaction_fee ?? 0;
                $settings['min_amount'] = $request->min_amount ?? 0;
                $settings['max_amount'] = $request->max_amount ?? 50000;
                break;

            case 'bank_transfer':
                $settings['bank_name'] = $request->bank_name;
                $settings['account_name'] = $request->account_name;
                $settings['account_number'] = $request->account_number;
                $settings['branch_name'] = $request->branch_name;
                $settings['routing_number'] = $request->routing_number;
                $settings['swift_code'] = $request->swift_code;
                $settings['transaction_fee'] = $request->transaction_fee ?? 0;
                $settings['min_amount'] = $request->min_amount ?? 0;
                $settings['max_amount'] = $request->max_amount ?? 100000;
                break;

            case 'cash_on_delivery':
                $settings['delivery_fee'] = $request->delivery_fee ?? 50;
                $settings['min_amount'] = $request->min_amount ?? 0;
                $settings['max_amount'] = $request->max_amount ?? 20000;
                break;
        }

        $paymentSetting->settings = $settings;
        $paymentSetting->save();

        return redirect()
            ->route('admin.payment-settings.index')
            ->with('success', 'Payment settings updated successfully.');
    }

    public function toggleStatus($gateway)
    {
        $paymentSetting = PaymentSetting::where('gateway', $gateway)->firstOrFail();
        $paymentSetting->is_active = !$paymentSetting->is_active;
        $paymentSetting->save();

        return response()->json([
            'success' => true,
            'is_active' => $paymentSetting->is_active,
            'message' => $paymentSetting->is_active ? 'Payment method enabled' : 'Payment method disabled'
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateways' => 'required|array',
            'gateways.*' => 'required|string|exists:payment_settings,gateway'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
        }

        foreach ($request->gateways as $index => $gateway) {
            $paymentSetting = PaymentSetting::where('gateway', $gateway)->first();
            if ($paymentSetting) {
                $paymentSetting->sort_order = $index + 1;
                $paymentSetting->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment methods order updated successfully'
        ]);
    }
}
