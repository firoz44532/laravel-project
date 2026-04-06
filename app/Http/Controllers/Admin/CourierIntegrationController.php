<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\CourierIntegration;
use App\Services\SteadfastCourierService;
use App\Services\PathaoCourierService;
use App\Services\eCourierService;
use App\Services\RedXService;
use App\Services\PaperflyService;
use App\Services\SundarbanService;
use App\Services\SAParibahanService;
use App\Services\JananiExpressService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\CourierIntegrationStoreRequest;
use App\Http\Requests\Admin\CourierBulkIntegrateRequest;

class CourierIntegrationController extends Controller
{
    protected $steadfastService;
    protected $pathaoService;
    protected $ecourierService;
    protected $redxService;
    protected $paperflyService;
    protected $sundarbanService;
    protected $saparibahanService;
    protected $jananiExpressService;

    public function __construct(
        SteadfastCourierService $steadfastService, 
        PathaoCourierService $pathaoService, 
        eCourierService $ecourierService, 
        RedXService $redxService,
        PaperflyService $paperflyService,
        SundarbanService $sundarbanService,
        SAParibahanService $saparibahanService,
        JananiExpressService $jananiExpressService
    ) {
        $this->steadfastService = $steadfastService;
        $this->pathaoService = $pathaoService;
        $this->ecourierService = $ecourierService;
        $this->redxService = $redxService;
        $this->paperflyService = $paperflyService;
        $this->sundarbanService = $sundarbanService;
        $this->saparibahanService = $saparibahanService;
        $this->jananiExpressService = $jananiExpressService;
    }

    /**
     * Show courier integration dashboard
     */
    public function index()
    {
        $integrations = CourierIntegration::with(['order'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total_integrations' => CourierIntegration::count(),
            'steadfast_count' => CourierIntegration::where('courier_type', 'steadfast')->count(),
            'pathao_count' => CourierIntegration::where('courier_type', 'pathao')->count(),
            'ecourier_count' => CourierIntegration::where('courier_type', 'ecourier')->count(),
            'redx_count' => CourierIntegration::where('courier_type', 'redx')->count(),
            'paperfly_count' => CourierIntegration::where('courier_type', 'paperfly')->count(),
            'sundarban_count' => CourierIntegration::where('courier_type', 'sundarban')->count(),
            'saparibahan_count' => CourierIntegration::where('courier_type', 'saparibahan')->count(),
            'janani_count' => CourierIntegration::where('courier_type', 'janani')->count(),
            'synced_count' => CourierIntegration::where('status', 'synced')->count(),
            'failed_count' => CourierIntegration::where('status', 'failed')->count(),
        ];

        return view('admin.courier-integrations.index', compact('integrations', 'stats'));
    }

    /**
     * Show form for creating new integration
     */
    public function create($orderId)
    {
        $order = Order::with(['shippingAddress', 'items.product', 'payment'])
            ->findOrFail($orderId);

        // Check if order already has integration
        $existingIntegration = CourierIntegration::where('order_id', $orderId)->first();
        if ($existingIntegration) {
            return redirect()->route('admin.courier-integrations.index')
                ->with('error', 'Order already integrated with courier service.');
        }

        return view('admin.courier-integrations.create', compact('order'));
    }

    /**
     * Store new courier integration
     */
    public function store(CourierIntegrationStoreRequest $request)
    {
        $validated = $request->validated();

        $order = Order::with(['shippingAddress', 'items.product', 'payment'])
            ->findOrFail($validated['order_id']);

        try {
            DB::beginTransaction();

            $result = null;
            
            if ($request->courier_type === 'steadfast') {
                $result = $this->steadfastService->createOrder($order);
            } elseif ($request->courier_type === 'pathao') {
                $result = $this->pathaoService->createOrder($order);
            } elseif ($request->courier_type === 'ecourier') {
                $result = $this->ecourierService->createOrder($order);
            } elseif ($request->courier_type === 'redx') {
                $result = $this->redxService->createOrder($order);
            } elseif ($request->courier_type === 'paperfly') {
                $result = $this->paperflyService->createOrder($order);
            } elseif ($request->courier_type === 'sundarban') {
                $result = $this->sundarbanService->createOrder($order);
            } elseif ($request->courier_type === 'saparibahan') {
                $result = $this->saparibahanService->createOrder($order);
            } elseif ($request->courier_type === 'janani') {
                $result = $this->jananiExpressService->createOrder($order);
            }

            if ($result['success']) {
                // Update order status to processing
                $order->update(['status' => 'processing']);

                DB::commit();

                return redirect()->route('admin.courier-integrations.index')
                    ->with('success', 'Order successfully integrated with ' . ucfirst($request->courier_type) . '. Tracking Number: ' . $result['tracking_number']);
            } else {
                DB::rollBack();
                
                return redirect()->back()
                    ->with('error', 'Failed to integrate with courier: ' . $result['message'])
                    ->withInput();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'System error: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show integration details
     */
    public function show($id)
    {
        $integration = CourierIntegration::with(['order', 'order.items.product', 'order.shippingAddress'])
            ->findOrFail($id);

        // Get tracking status from courier API
        $trackingInfo = null;
        if ($integration->tracking_number && $integration->status === 'synced') {
            if ($integration->courier_type === 'steadfast') {
                $trackingInfo = $this->steadfastService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'pathao') {
                $trackingInfo = $this->pathaoService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'ecourier') {
                $trackingInfo = $this->ecourierService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'redx') {
                $trackingInfo = $this->redxService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'paperfly') {
                $trackingInfo = $this->paperflyService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'sundarban') {
                $trackingInfo = $this->sundarbanService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'saparibahan') {
                $trackingInfo = $this->saparibahanService->trackOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'janani') {
                $trackingInfo = $this->jananiExpressService->trackOrder($integration->tracking_number);
            }
        }

        return view('admin.courier-integrations.show', compact('integration', 'trackingInfo'));
    }

    /**
     * Cancel courier integration
     */
    public function cancel($id)
    {
        $integration = CourierIntegration::findOrFail($id);

        if ($integration->status !== 'synced') {
            return redirect()->back()
                ->with('error', 'Cannot cancel integration with status: ' . $integration->status);
        }

        try {
            $result = null;
            
            if ($integration->courier_type === 'steadfast') {
                $result = $this->steadfastService->cancelOrder($integration->consignment_id);
            } elseif ($integration->courier_type === 'pathao') {
                $result = $this->pathaoService->cancelOrder($integration->consignment_id);
            } elseif ($integration->courier_type === 'ecourier') {
                $result = $this->ecourierService->cancelOrder($integration->consignment_id);
            } elseif ($integration->courier_type === 'redx') {
                $result = $this->redxService->cancelOrder($integration->tracking_number);
            } elseif ($integration->courier_type === 'paperfly') {
                $result = $this->paperflyService->cancelOrder($integration->consignment_id);
            } elseif ($integration->courier_type === 'sundarban') {
                $result = $this->sundarbanService->cancelOrder($integration->consignment_id);
            } elseif ($integration->courier_type === 'saparibahan') {
                $result = $this->saparibahanService->cancelOrder($integration->consignment_id);
            } elseif ($integration->courier_type === 'janani') {
                $result = $this->jananiExpressService->cancelOrder($integration->consignment_id);
            }

            if ($result['success']) {
                $integration->update(['status' => 'cancelled']);
                
                return redirect()->back()
                    ->with('success', 'Courier integration cancelled successfully.');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to cancel integration: ' . $result['message']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'System error: ' . $e->getMessage());
        }
    }

    /**
     * Bulk integrate orders
     */
    public function bulkIntegrate(CourierBulkIntegrateRequest $request)
    {
        $validated = $request->validated();

        $orderIds = $validated['order_ids'];
        $courierType = $validated['courier_type'];
        $successCount = 0;
        $failedOrders = [];

        foreach ($orderIds as $orderId) {
            $order = Order::with(['shippingAddress', 'items.product', 'payment'])
                ->findOrFail($orderId);

            // Check if already integrated
            $existingIntegration = CourierIntegration::where('order_id', $orderId)->first();
            if ($existingIntegration) {
                $failedOrders[] = "Order #{$order->order_number}: Already integrated";
                continue;
            }

            try {
                $result = null;
                
                if ($courierType === 'steadfast') {
                    $result = $this->steadfastService->createOrder($order);
                } elseif ($courierType === 'pathao') {
                    $result = $this->pathaoService->createOrder($order);
                } elseif ($courierType === 'ecourier') {
                    $result = $this->ecourierService->createOrder($order);
                } elseif ($courierType === 'redx') {
                    $result = $this->redxService->createOrder($order);
                } elseif ($courierType === 'paperfly') {
                    $result = $this->paperflyService->createOrder($order);
                } elseif ($courierType === 'sundarban') {
                    $result = $this->sundarbanService->createOrder($order);
                } elseif ($courierType === 'saparibahan') {
                    $result = $this->saparibahanService->createOrder($order);
                } elseif ($courierType === 'janani') {
                    $result = $this->jananiExpressService->createOrder($order);
                }

                if ($result['success']) {
                    $order->update(['status' => 'processing']);
                    $successCount++;
                } else {
                    $failedOrders[] = "Order #{$order->order_number}: " . $result['message'];
                }

            } catch (\Exception $e) {
                $failedOrders[] = "Order #{$order->order_number}: " . $e->getMessage();
            }
        }

        $message = "Successfully integrated {$successCount} orders with " . ucfirst($courierType);
        if (!empty($failedOrders)) {
            $message .= ". Failed: " . implode('; ', array_slice($failedOrders, 0, 3));
            if (count($failedOrders) > 3) {
                $message .= " and " . (count($failedOrders) - 3) . " more.";
            }
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Retry failed integration
     */
    public function retry($id)
    {
        $integration = CourierIntegration::with(['order', 'order.items.product', 'order.shippingAddress', 'order.payment'])
            ->findOrFail($id);

        if ($integration->status !== 'failed') {
            return redirect()->back()
                ->with('error', 'Can only retry failed integrations.');
        }

        try {
            // Delete the failed integration
            $integration->delete();

            // Create new integration
            $result = null;
            
            if ($integration->courier_type === 'steadfast') {
                $result = $this->steadfastService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'pathao') {
                $result = $this->pathaoService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'ecourier') {
                $result = $this->ecourierService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'redx') {
                $result = $this->redxService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'paperfly') {
                $result = $this->paperflyService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'sundarban') {
                $result = $this->sundarbanService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'saparibahan') {
                $result = $this->saparibahanService->createOrder($integration->order);
            } elseif ($integration->courier_type === 'janani') {
                $result = $this->jananiExpressService->createOrder($integration->order);
            }

            if ($result['success']) {
                $integration->order->update(['status' => 'processing']);
                
                return redirect()->route('admin.courier-integrations.index')
                    ->with('success', 'Integration retry successful. Tracking Number: ' . $result['tracking_number']);
            } else {
                return redirect()->back()
                    ->with('error', 'Retry failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'System error: ' . $e->getMessage());
        }
    }

    /**
     * Get integration statistics
     */
    public function stats()
    {
        $stats = [
            'total' => CourierIntegration::count(),
            'by_courier' => CourierIntegration::selectRaw('courier_type, count(*) as count')
                ->groupBy('courier_type')
                ->pluck('count', 'courier_type')
                ->toArray(),
            'by_status' => CourierIntegration::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'recent' => CourierIntegration::with(['order'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}
