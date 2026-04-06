<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\StockAlert;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests\Admin\InventoryAdjustStockRequest;
use App\Http\Requests\Admin\InventoryBulkUpdateRequest;

class InventoryController extends Controller
{
    public function dashboard()
    {
        // Get inventory statistics
        $stats = [
            'total_products' => Product::count(),
            'in_stock' => Product::where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            'low_stock' => StockAlert::active()->lowStock()->count(),
            'total_stock_value' => Product::selectRaw('SUM(stock_quantity * price) as total_value')->first()->total_value ?? 0,
        ];

        // Get low stock products
        $lowStockProducts = Product::whereHas('stockAlert', function ($query) {
            $query->active()->whereColumn('products.stock_quantity', '<=', 'stock_alerts.threshold_quantity');
        })
        ->with(['stockAlert', 'category'])
        ->get();

        // Get recent stock movements
        $recentMovements = InventoryLog::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get stock movement chart data (last 7 days)
        $stockMovements = InventoryLog::selectRaw('DATE(created_at) as date, action, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date', 'action')
            ->orderBy('date')
            ->get();

        // Get top products by stock value
        $topStockValue = Product::select('name', 'stock_quantity', 'price', DB::raw('stock_quantity * price as total_value'))
            ->where('stock_quantity', '>', 0)
            ->orderBy('total_value', 'desc')
            ->take(10)
            ->get();

        return view('admin.inventory.dashboard', compact(
            'stats',
            'lowStockProducts',
            'recentMovements',
            'stockMovements',
            'topStockValue'
        ));
    }

    public function index()
    {
        $products = Product::with(['category', 'stockAlert'])
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('sku', 'like', '%' . request('search') . '%');
            })
            ->when(request('stock_status'), function ($query) {
                switch (request('stock_status')) {
                    case 'in_stock':
                        $query->where('stock_quantity', '>', 0);
                        break;
                    case 'low_stock':
                        $query->whereHas('stockAlert', function ($q) {
                            $q->active()->whereColumn('products.stock_quantity', '<=', 'stock_alerts.threshold_quantity');
                        });
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', '<=', 0);
                        break;
                }
            })
            ->when(request('category'), function ($query) {
                $query->where('category_id', request('category'));
            })
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.inventory.index', compact('products', 'categories'));
    }

    public function adjustStock(InventoryAdjustStockRequest $request, Product $product)
    {
        $validated = $request->validated();

        $quantityBefore = $product->stock_quantity;
        $quantityChange = $validated['action'] === 'stock_out' ? -$validated['quantity'] : $validated['quantity'];
        $quantityAfter = $quantityBefore + $quantityChange;

        // Don't allow negative stock for stock_out
        if ($validated['action'] === 'stock_out' && $quantityAfter < 0) {
            return back()->with('error', 'Insufficient stock for this operation.');
        }

        DB::beginTransaction();
        try {
            // Update product stock
            $product->update(['stock_quantity' => $quantityAfter]);

            // Create inventory log
            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'action' => $validated['action'],
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'quantity_change' => $quantityChange,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            return back()->with('success', 'Stock adjusted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }

    public function stockHistory(Product $product)
    {
        $logs = InventoryLog::with(['user'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.history', compact('product', 'logs'));
    }

    public function bulkUpdate()
    {
        return view('admin.inventory.bulk-update');
    }

    public function bulkUpdateStore(InventoryBulkUpdateRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            foreach ($validated['updates'] as $update) {
                $product = Product::find($update['product_id']);
                $quantityBefore = $product->stock_quantity;
                $quantityChange = $update['action'] === 'stock_out' ? -$update['quantity'] : $update['quantity'];
                $quantityAfter = $quantityBefore + $quantityChange;

                // Don't allow negative stock for stock_out
                if ($update['action'] === 'stock_out' && $quantityAfter < 0) {
                    continue; // Skip this update
                }

                $product->update(['stock_quantity' => $quantityAfter]);

                InventoryLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'action' => $update['action'],
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'quantity_change' => $quantityChange,
                    'reason' => $update['reason'],
                    'notes' => 'Bulk update',
                ]);
            }

            DB::commit();
            return back()->with('success', 'Bulk stock update completed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to complete bulk update: ' . $e->getMessage());
        }
    }

    public function alerts()
    {
        $alerts = StockAlert::with(['product'])
            ->when(request('status'), function ($query) {
                if (request('status') === 'active') {
                    $query->active();
                } elseif (request('status') === 'sent') {
                    $query->where('is_sent', true);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory.alerts', compact('alerts'));
    }

    public function updateAlert(Request $request, StockAlert $alert)
    {
        $request->validate([
            'threshold_quantity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'alert_type' => 'required|in:email,sms,dashboard',
        ]);

        $alert->update($request->all());

        return back()->with('success', 'Alert updated successfully!');
    }

    public function reports()
    {
        $reports = [
            'stock_movements' => $this->getStockMovementReport(),
            'low_stock_analysis' => $this->getLowStockAnalysis(),
            'inventory_valuation' => $this->getInventoryValuation(),
        ];

        return view('admin.inventory.reports', compact('reports'));
    }

    private function getStockMovementReport()
    {
        return InventoryLog::selectRaw('action, COUNT(*) as count, SUM(ABS(quantity_change)) as total_quantity')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('action')
            ->get();
    }

    private function getLowStockAnalysis()
    {
        return Product::whereHas('stockAlert', function ($query) {
            $query->active()->whereColumn('products.stock_quantity', '<=', 'stock_alerts.threshold_quantity');
        })
        ->with(['category', 'stockAlert'])
        ->get();
    }

    private function getInventoryValuation()
    {
        return [
            'total_products' => Product::count(),
            'total_quantity' => Product::sum('stock_quantity'),
            'total_value' => Product::selectRaw('SUM(stock_quantity * price) as total_value')->first()->total_value ?? 0,
            'average_value_per_product' => Product::selectRaw('AVG(stock_quantity * price) as avg_value')->first()->avg_value ?? 0,
        ];
    }
}
