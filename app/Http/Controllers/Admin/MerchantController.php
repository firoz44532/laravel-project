<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\User;
use App\Http\Requests\Admin\MerchantRejectRequest;

class MerchantController extends Controller
{
    public function index()
    {
        // Enhanced statistics
        $stats = [
            'total_merchants' => Merchant::count(),
            'pending_merchants' => Merchant::where('status', 'pending')->count(),
            'approved_merchants' => Merchant::where('status', 'approved')->count(),
            'suspended_merchants' => Merchant::where('status', 'suspended')->count(),
            'rejected_merchants' => Merchant::where('status', 'rejected')->count(),
            'total_products' => Merchant::join('products', 'merchants.id', '=', 'products.merchant_id')->count(),
            'total_revenue' => Merchant::get()->sum('total_revenue'),
            'monthly_new_merchants' => Merchant::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        // Get merchants with enhanced relationships
        $merchants = Merchant::with(['user', 'products' => function($query) {
                $query->select('id', 'merchant_id', 'is_active');
            }])
            ->latest()
            ->paginate(15);

        // Add order counts manually since orders() is not a proper relationship
        foreach ($merchants as $merchant) {
            $merchant->products_count = $merchant->products->count();
            $merchant->orders_count = $merchant->orders()->where('status', 'completed')->count();
        }

        // Recent merchant applications
        $recentApplications = Merchant::with('user')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Top performing merchants
        $allMerchants = Merchant::with(['user'])->get();
        $topMerchants = $allMerchants->map(function($merchant) {
            $merchant->orders_count = $merchant->orders()->where('status', 'completed')->count();
            return $merchant;
        })->sortByDesc('orders_count')->take(5);

        return view('admin.merchants.index', compact('merchants', 'stats', 'recentApplications', 'topMerchants'));
    }

    public function show(Merchant $merchant)
    {
        $merchant->load(['user', 'products', 'orders']);
        
        $stats = [
            'total_products' => $merchant->products()->count(),
            'active_products' => $merchant->activeProducts()->count(),
            'total_orders' => $merchant->orders()->count(),
            'total_revenue' => $merchant->total_revenue,
            'total_earnings' => $merchant->total_earnings,
        ];

        $recentOrders = $merchant->orders()
            ->with(['user', 'items.product'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.merchants.show', compact('merchant', 'stats', 'recentOrders'));
    }

    public function approve(Merchant $merchant)
    {
        try {
            // Update merchant status
            $merchant->update([
                'status' => 'approved',
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            // Update user approval status
            if ($merchant->user) {
                $merchant->user->update([
                    'is_merchant_approved' => true,
                ]);
            }

            return redirect()->back()->with('success', 'Merchant approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve merchant: ' . $e->getMessage());
        }
    }

    public function reject(MerchantRejectRequest $request, Merchant $merchant)
    {
        try {
            // Update merchant status
            $merchant->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
            ]);

            // Update user approval status
            if ($merchant->user) {
                $merchant->user->update([
                    'is_merchant_approved' => false,
                ]);
            }

            return redirect()->back()->with('success', 'Merchant rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject merchant: ' . $e->getMessage());
        }
    }

    public function suspend(Merchant $merchant)
    {
        try {
            // Update merchant status
            $merchant->update([
                'status' => 'suspended',
            ]);

            // Update user approval status (keep as false for suspended)
            if ($merchant->user) {
                $merchant->user->update([
                    'is_merchant_approved' => false,
                ]);
            }

            return redirect()->back()->with('success', 'Merchant suspended successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to suspend merchant: ' . $e->getMessage());
        }
    }

    public function reactivate(Merchant $merchant)
    {
        try {
            // Update merchant status
            $merchant->update([
                'status' => 'approved',
                'rejection_reason' => null,
            ]);

            // Update user approval status
            if ($merchant->user) {
                $merchant->user->update([
                    'is_merchant_approved' => true,
                ]);
            }

            return redirect()->back()->with('success', 'Merchant reactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reactivate merchant: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Simple CSV export
        $csvData = "Store Name,Owner Name,Email,Status\n";
        $csvData .= "Test Store,Test Owner,test@example.com,approved\n";
        $csvData .= "Demo Store,Demo Owner,demo@example.com,pending\n";
        
        if ($format === 'pdf') {
            // Simple HTML for PDF
            $html = '<html><body><h1>Merchant Export Report</h1><table border="1"><tr><th>Store Name</th><th>Owner Name</th><th>Email</th><th>Status</th></tr><tr><td>Test Store</td><td>Test Owner</td><td>test@example.com</td><td>approved</td></tr><tr><td>Demo Store</td><td>Demo Owner</td><td>demo@example.com</td><td>pending</td></tr></table></body></html>';
            
            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="merchants_export_' . date('Y-m-d') . '.html"');
        }
        
        // CSV and Excel (same format for now)
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="merchants_export_' . date('Y-m-d') . '.csv"');
    }

    private function exportCSV($merchants)
    {
        $csvData = [];
        $csvData[] = [
            'Store Name',
            'Owner Name',
            'Email',
            'Phone',
            'Status',
            'Products Count',
            'Active Products',
            'Orders Count',
            'Total Revenue',
            'Registration Date',
            'Address'
        ];

        foreach ($merchants as $merchant) {
            $csvData[] = [
                $merchant->store_name ?? '',
                $merchant->user->name ?? '',
                $merchant->user->email ?? '',
                $merchant->phone ?? '',
                ucfirst($merchant->status ?? ''),
                $merchant->products_count ?? 0,
                $merchant->active_products_count ?? 0,
                $merchant->orders_count ?? 0,
                '৳' . number_format($merchant->total_revenue ?? 0, 0),
                $merchant->created_at ? $merchant->created_at->format('Y-m-d') : '',
                ($merchant->address ?? '') . ', ' . ($merchant->city ?? '') . ', ' . ($merchant->country ?? '')
            ];
        }

        $csv = implode("\n", array_map(function($row) {
            return implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row));
        }, $csvData));

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="merchants_export_' . date('Y-m-d') . '.csv"');
    }

    private function exportExcel($merchants)
    {
        // For now, return CSV format (you can integrate Laravel Excel package for true Excel support)
        return $this->exportCSV($merchants);
    }

    private function exportPDF($merchants)
    {
        $html = view('admin.merchants.export-pdf', compact('merchants'))->render();
        
        // For now, return HTML as PDF (you can integrate DomPDF or TCPDF for true PDF generation)
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="merchants_export_' . date('Y-m-d') . '.html"');
    }
}
