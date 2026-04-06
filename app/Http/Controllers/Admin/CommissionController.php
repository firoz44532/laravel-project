<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Order;
use App\Models\User;
use App\Http\Requests\Admin\CommissionSettingsUpdateRequest;
use App\Http\Requests\Admin\CommissionPayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index()
    {
        $commissionSettings = Setting::getByGroup('commission');
        
        // Get commission statistics
        $stats = [
            'total_commission_earned' => $this->calculateTotalCommission(),
            'pending_payouts' => $this->getPendingPayouts(),
            'total_paid' => $this->getTotalPaid(),
            'merchant_count' => User::where('role', 'merchant')->count(),
            'affiliate_count' => User::where('role', 'affiliate')->count(),
        ];

        return view('admin.commissions.index', compact('commissionSettings', 'stats'));
    }

    public function updateSettings(CommissionSettingsUpdateRequest $request)
    {
        foreach ($request->settings as $settingData) {
            $setting = Setting::find($settingData['id']);
            if ($setting) {
                $setting->setValue($settingData['value'] ?? '');
                $setting->save();
            }
        }

        return redirect()
            ->route('admin.commissions.index')
            ->with('success', 'Commission settings updated successfully.');
    }

    public function reports()
    {
        $commissions = $this->getCommissionReports();
        
        return view('admin.commissions.reports', compact('commissions'));
    }

    public function payouts()
    {
        $pendingPayouts = $this->getPendingPayoutsDetails();
        
        return view('admin.commissions.payouts', compact('pendingPayouts'));
    }

    public function processPayout(CommissionPayoutRequest $request)
    {
        // Process the payout logic here
        // This would typically involve creating a payout record and updating user balance

        return redirect()
            ->route('admin.commissions.payouts')
            ->with('success', 'Payout processed successfully.');
    }

    private function calculateTotalCommission()
    {
        // For now, return a placeholder value since commission tracking table doesn't exist yet
        // This would typically query a commissions table or calculate based on order items
        return 2450.00;
    }

    private function getPendingPayouts()
    {
        // This would typically query a commissions table
        // For now, return a placeholder value
        return 1245.00;
    }

    private function getTotalPaid()
    {
        // This would typically query a payouts table
        // For now, return a placeholder value
        return 3750.00;
    }

    private function getCommissionReports()
    {
        // Return commission reports data
        return collect();
    }

    private function getPendingPayoutsDetails()
    {
        // Return detailed pending payouts
        return collect();
    }
}
