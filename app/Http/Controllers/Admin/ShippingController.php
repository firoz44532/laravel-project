<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ShippingZoneStoreRequest;
use App\Http\Requests\Admin\ShippingZoneUpdateRequest;
use App\Http\Requests\Admin\ShippingMethodStoreRequest;
use App\Http\Requests\Admin\ShippingMethodUpdateRequest;
use App\Http\Requests\Admin\ShippingSettingsUpdateRequest;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Display shipping settings overview.
     */
    public function index()
    {
        $zones = $this->shippingService->getShippingZones();
        $methods = $this->shippingService->getShippingMethods();
        $settings = $this->shippingService->getShippingSettings();
        
        return view('admin.shipping.index', compact('zones', 'methods', 'settings'));
    }

    /**
     * Display shipping zones management.
     */
    public function zones()
    {
        $zones = $this->shippingService->getShippingZones();
        return view('admin.shipping.zones.index', compact('zones'));
    }

    /**
     * Show form to create shipping zone.
     */
    public function createZone()
    {
        $methods = ShippingMethod::active()->ordered()->get();
        return view('admin.shipping.zones.create', compact('methods'));
    }

    /**
     * Store new shipping zone.
     */
    public function storeZone(ShippingZoneStoreRequest $request)
    {

        // Process cities and areas from comma-separated strings
        $cities = $request->cities ? array_map('trim', explode(',', $request->cities)) : null;
        $areas = $request->areas ? array_map('trim', explode(',', $request->areas)) : null;

        $zone = ShippingZone::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'default_cost' => $request->default_cost,
            'express_cost' => $request->express_cost,
            'delivery_days' => $request->delivery_days,
            'express_days' => $request->express_days,
            'cities' => $cities,
            'areas' => $areas,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Attach shipping methods with rates
        if ($request->has('methods')) {
            foreach ($request->methods as $methodId) {
                $cost = $request->method_costs[$methodId] ?? 0;
                $zone->shippingMethods()->attach($methodId, [
                    'cost' => $cost,
                    'additional_cost_per_kg' => 0,
                    'free_shipping_threshold' => null,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()
            ->route('admin.shipping.zones')
            ->with('success', 'Shipping zone created successfully.');
    }

    /**
     * Show form to edit shipping zone.
     */
    public function editZone(ShippingZone $zone)
    {
        $zone->load('shippingMethods');
        $methods = ShippingMethod::active()->ordered()->get();
        return view('admin.shipping.zones.edit', compact('zone', 'methods'));
    }

    /**
     * Update shipping zone.
     */
    public function updateZone(ShippingZoneUpdateRequest $request, ShippingZone $zone)
    {

        // Process cities and areas from comma-separated strings
        $cities = $request->cities ? array_map('trim', explode(',', $request->cities)) : null;
        $areas = $request->areas ? array_map('trim', explode(',', $request->areas)) : null;

        $zone->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'default_cost' => $request->default_cost,
            'express_cost' => $request->express_cost,
            'delivery_days' => $request->delivery_days,
            'express_days' => $request->express_days,
            'cities' => $cities,
            'areas' => $areas,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Sync shipping methods with rates
        if ($request->has('methods')) {
            $syncData = [];
            foreach ($request->methods as $methodId) {
                $cost = $request->method_costs[$methodId] ?? 0;
                $syncData[$methodId] = [
                    'cost' => $cost,
                    'additional_cost_per_kg' => 0,
                    'free_shipping_threshold' => null,
                    'is_active' => true,
                ];
            }
            $zone->shippingMethods()->sync($syncData);
        } else {
            $zone->shippingMethods()->detach();
        }

        return redirect()
            ->route('admin.shipping.zones')
            ->with('success', 'Shipping zone updated successfully.');
    }

    /**
     * Delete shipping zone.
     */
    public function destroyZone(ShippingZone $zone)
    {
        $zone->shippingMethods()->detach();
        $zone->delete();

        return redirect()
            ->route('admin.shipping.zones')
            ->with('success', 'Shipping zone deleted successfully.');
    }

    /**
     * Display shipping methods management.
     */
    public function methods()
    {
        $methods = $this->shippingService->getShippingMethods();
        return view('admin.shipping.methods.index', compact('methods'));
    }

    /**
     * Show form to create shipping method.
     */
    public function createMethod()
    {
        return view('admin.shipping.methods.create');
    }

    /**
     * Store new shipping method.
     */
    public function storeMethod(ShippingMethodStoreRequest $request)
    {

        ShippingMethod::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'estimated_days' => $request->estimated_days,
            'base_cost' => $request->base_cost,
            'is_active' => $request->boolean('is_active', true),
            'tracking_available' => $request->boolean('tracking_available', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('admin.shipping.methods')
            ->with('success', 'Shipping method created successfully.');
    }

    /**
     * Show form to edit shipping method.
     */
    public function editMethod(ShippingMethod $method)
    {
        return view('admin.shipping.methods.edit', compact('method'));
    }

    /**
     * Update shipping method.
     */
    public function updateMethod(ShippingMethodUpdateRequest $request, ShippingMethod $method)
    {

        $method->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'estimated_days' => $request->estimated_days,
            'base_cost' => $request->base_cost,
            'is_active' => $request->boolean('is_active', true),
            'tracking_available' => $request->boolean('tracking_available', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()
            ->route('admin.shipping.methods')
            ->with('success', 'Shipping method updated successfully.');
    }

    /**
     * Delete shipping method.
     */
    public function destroyMethod(ShippingMethod $method)
    {
        $method->shippingZones()->detach();
        $method->delete();

        return redirect()
            ->route('admin.shipping.methods')
            ->with('success', 'Shipping method deleted successfully.');
    }

    /**
     * Display shipping settings.
     */
    public function settings()
    {
        $settings = ShippingSetting::getGroupSettings('general');
        $taxSettings = ShippingSetting::getGroupSettings('tax');
        
        return view('admin.shipping.settings', compact('settings', 'taxSettings'));
    }

    /**
     * Update shipping settings.
     */
    public function updateSettings(ShippingSettingsUpdateRequest $request)
    {

        // Update general settings
        $this->shippingService->updateShippingSetting('default_shipping_cost', $request->default_shipping_cost);
        $this->shippingService->updateShippingSetting('free_shipping_threshold', $request->free_shipping_threshold);
        $this->shippingService->updateShippingSetting('weight_based_enabled', $request->boolean('weight_based_enabled'));
        $this->shippingService->updateShippingSetting('order_value_based_enabled', $request->boolean('order_value_based_enabled'));

        // Update tax settings
        $this->shippingService->updateShippingSetting('tax_enabled', $request->boolean('tax_enabled'));
        $this->shippingService->updateShippingSetting('vat_rate', $request->vat_rate);
        $this->shippingService->updateShippingSetting('shipping_taxable', $request->boolean('shipping_taxable'));
        $this->shippingService->updateShippingSetting('tax_inclusive', $request->boolean('tax_inclusive'));

        return redirect()
            ->route('admin.shipping.settings')
            ->with('success', 'Shipping settings updated successfully.');
    }

    /**
     * API endpoint to calculate shipping cost.
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'cart_total' => 'required|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'city' => 'nullable|string',
            'area' => 'nullable|string',
        ]);

        $result = $this->shippingService->calculateShipping(
            $request->cart_total,
            $request->weight ?? 0,
            $request->city,
            $request->area
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * API endpoint to get available shipping methods.
     */
    public function getShippingMethods(Request $request)
    {
        $request->validate([
            'city' => 'nullable|string',
            'area' => 'nullable|string',
            'cart_total' => 'nullable|numeric|min:0',
        ]);

        $methods = $this->shippingService->getAvailableShippingMethods(
            $request->city,
            $request->area,
            $request->cart_total ?? 0
        );

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }
}
