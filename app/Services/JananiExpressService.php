<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CourierIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JananiExpressService
{
    protected $baseUrl;
    protected $apiKey;
    protected $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('services.janani.base_url', 'https://jananiexpress.com');
        $this->apiKey = config('services.janani.api_key');
        $this->merchantId = config('services.janani.merchant_id');
    }

    /**
     * Create order in Janani Express system
     */
    public function createOrder(Order $order)
    {
        try {
            $payload = $this->prepareOrderPayload($order);
            
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
                'MERCHANT-ID' => $this->merchantId,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/v1/create-order', $payload);

            $responseData = $response->json();

            // Create courier integration record
            $courierIntegration = CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'janani',
                'status' => $response->successful() ? 'synced' : 'failed',
                'pickup_address' => $payload['pickup_address'],
                'delivery_address' => $payload['delivery_address'],
                'customer_name' => $payload['recipient_name'],
                'customer_phone' => $payload['recipient_phone'],
                'package_weight' => $payload['package_weight'] ?? 0.5,
                'package_description' => $payload['package_description'],
                'cod_amount' => $payload['cod_amount'] ?? 0,
                'delivery_charge' => $responseData['delivery_charge'] ?? 0,
                'api_response' => $responseData,
                'error_message' => $response->successful() ? null : ($responseData['message'] ?? 'Unknown error'),
                'synced_at' => $response->successful() ? now() : null,
            ]);

            if ($response->successful() && isset($responseData['tracking_code'])) {
                $courierIntegration->update([
                    'tracking_number' => $responseData['tracking_code'],
                    'consignment_id' => $responseData['consignment_id'] ?? null,
                ]);

                // Update order with tracking info
                $order->update([
                    'notes' => 'Janani Express: ' . $responseData['tracking_code'] . ' (Consignment: ' . ($responseData['consignment_id'] ?? 'N/A') . ')'
                ]);

                return [
                    'success' => true,
                    'tracking_number' => $responseData['tracking_code'],
                    'consignment_id' => $responseData['consignment_id'] ?? null,
                    'message' => 'Order successfully created in Janani Express system'
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to create order in Janani Express',
                'errors' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Janani Express API Error: ' . $e->getMessage());
            
            // Create failed integration record
            CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'janani',
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
    public function trackOrder($trackingCode)
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
                'MERCHANT-ID' => $this->merchantId,
            ])->get($this->baseUrl . '/api/v1/track-order/' . $trackingCode);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Janani Express Tracking Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder($consignmentId)
    {
        try {
            $response = Http::withHeaders([
                'API-KEY' => $this->apiKey,
                'MERCHANT-ID' => $this->merchantId,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/v1/cancel-order', [
                'consignment_id' => $consignmentId
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                // Update integration record
                CourierIntegration::where('courier_type', 'janani')
                    ->where('consignment_id', $consignmentId)
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
            Log::error('Janani Express Cancel Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare order payload for Janani Express API
     */
    private function prepareOrderPayload(Order $order)
    {
        $shippingAddress = $order->shippingAddress;
        
        return [
            'recipient_name' => $shippingAddress->first_name . ' ' . $shippingAddress->last_name,
            'recipient_phone' => $shippingAddress->phone,
            'recipient_address' => $shippingAddress->address,
            'recipient_city' => $shippingAddress->city ?? 'Dhaka',
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
        ];
    }

    /**
     * Get pickup address
     */
    private function getPickupAddress()
    {
        return config('services.janani.pickup_address', 'Shop Address, Dhaka');
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
