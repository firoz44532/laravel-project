<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CourierIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedXService
{
    protected $baseUrl;
    protected $apiKey;
    protected $storeId;

    public function __construct()
    {
        $this->baseUrl = config('services.redx.base_url', 'https://redx.com.bd');
        $this->apiKey = config('services.redx.api_key');
        $this->storeId = config('services.redx.store_id');
    }

    /**
     * Create order in RedX system
     */
    public function createOrder(Order $order)
    {
        try {
            $payload = $this->prepareOrderPayload($order);
            
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/orders', $payload);

            $responseData = $response->json();

            // Create courier integration record
            $courierIntegration = CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'redx',
                'status' => $response->successful() ? 'synced' : 'failed',
                'pickup_address' => $payload['pickup_address'],
                'delivery_address' => $payload['delivery_address'],
                'customer_name' => $payload['customer_name'],
                'customer_phone' => $payload['customer_phone'],
                'package_weight' => $payload['package_weight'] ?? 0.5,
                'package_description' => $payload['package_description'],
                'cod_amount' => $payload['cod_amount'] ?? 0,
                'delivery_charge' => $responseData['delivery_fee'] ?? 0,
                'api_response' => $responseData,
                'error_message' => $response->successful() ? null : ($responseData['message'] ?? 'Unknown error'),
                'synced_at' => $response->successful() ? now() : null,
            ]);

            if ($response->successful() && isset($responseData['tracking_id'])) {
                $courierIntegration->update([
                    'tracking_number' => $responseData['tracking_id'],
                    'consignment_id' => $responseData['consignment_id'] ?? null,
                ]);

                // Update order with tracking info
                $order->update([
                    'notes' => 'RedX: ' . $responseData['tracking_id'] . ' (Consignment: ' . ($responseData['consignment_id'] ?? 'N/A') . ')'
                ]);

                return [
                    'success' => true,
                    'tracking_number' => $responseData['tracking_id'],
                    'consignment_id' => $responseData['consignment_id'] ?? null,
                    'message' => 'Order successfully created in RedX system'
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to create order in RedX',
                'errors' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('RedX API Error: ' . $e->getMessage());
            
            // Create failed integration record
            CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'redx',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'api_response' => ['error' => $e->getMessage()],
            ]);

            return [
                'success' => false,
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Track order status
     */
    public function trackOrder($trackingId)
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
            ])->get($this->baseUrl . '/v1/orders/track/' . $trackingId);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('RedX Tracking Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder($trackingId)
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/orders/cancel', [
                'tracking_id' => $trackingId
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                // Update integration record
                CourierIntegration::where('courier_type', 'redx')
                    ->where('tracking_number', $trackingId)
                    ->update(['status' => 'cancelled']);

                return [
                    'success' => true,
                    'message' => 'Order cancelled successfully'
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to cancel order'
            ];

        } catch (\Exception $e) {
            Log::error('RedX Cancel Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get delivery areas
     */
    public function getDeliveryAreas()
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
            ])->get($this->baseUrl . '/v1/delivery-areas');

            return $response->json();

        } catch (\Exception $e) {
            Log::error('RedX Areas Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate delivery charge
     */
    public function calculateDeliveryCharge($data)
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/calculate-delivery-charge', $data);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('RedX Charge Calculation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get store information
     */
    public function getStoreInfo()
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
            ])->get($this->baseUrl . '/v1/stores/' . $this->storeId);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('RedX Store Info Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare order payload for RedX API
     */
    private function prepareOrderPayload(Order $order)
    {
        $shippingAddress = $order->shippingAddress;
        
        return [
            'store_id' => $this->storeId,
            'customer_name' => $shippingAddress->first_name . ' ' . $shippingAddress->last_name,
            'customer_phone' => $shippingAddress->phone,
            'customer_address' => $shippingAddress->address,
            'customer_city' => $shippingAddress->city ?? 'Dhaka',
            'customer_area' => $shippingAddress->city ?? 'Dhaka',
            'pickup_address' => $this->getPickupAddress(),
            'delivery_address' => $shippingAddress->address,
            'package_weight' => $this->calculatePackageWeight($order),
            'package_description' => $this->getPackageDescription($order),
            'cod_amount' => $order->payment && $order->payment->method === 'cod' ? $order->total_amount : 0,
            'product_type' => 'parcel',
            'delivery_type' => 'regular',
            'special_instruction' => $order->notes ?? 'Handle with care',
            'product_value' => $order->total_amount,
            'item_quantity' => $order->items->count(),
            'payment_method' => $order->payment && $order->payment->method === 'cod' ? 'COD' : 'PREPAID',
        ];
    }

    /**
     * Get pickup address
     */
    private function getPickupAddress()
    {
        return config('services.redx.pickup_address', 'Shop Address, Dhaka');
    }

    /**
     * Calculate package weight
     */
    private function calculatePackageWeight(Order $order)
    {
        $totalWeight = 0;
        foreach ($order->items as $item) {
            $productWeight = $item->product->weight ?? 0.5;
            $totalWeight += $productWeight * $item->quantity;
        }
        
        return max($totalWeight, 0.5); // Minimum 0.5kg
    }

    /**
     * Get package description
     */
    private function getPackageDescription(Order $order)
    {
        $itemNames = $order->items->take(3)->pluck('product.name')->implode(', ');
        $itemCount = $order->items->count();
        
        if ($itemCount > 3) {
            $itemNames .= ' and ' . ($itemCount - 3) . ' more items';
        }
        
        return "Order #{$order->order_number}: {$itemNames}";
    }
}
