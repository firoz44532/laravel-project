<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;

class TestExportController extends Controller
{
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            
            // Simple test first
            if ($format === 'test') {
                return response()->json(['message' => 'TestExportController is working!']);
            }
            
            // Return simple CSV for testing
            $csvData = "Store Name,Owner Name,Email,Status\n";
            $csvData .= "Test Store,Test Owner,test@example.com,approved\n";
            
            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="test_export.csv"');
                
        } catch (\Exception $e) {
            \Log::error('Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
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
        // For now, return CSV format
        return $this->exportCSV($merchants);
    }

    private function exportPDF($merchants)
    {
        $html = view('admin.merchants.export-pdf', compact('merchants'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="merchants_export_' . date('Y-m-d') . '.html"');
    }
}
