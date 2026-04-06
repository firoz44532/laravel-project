<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CourierIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastCourierService
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.steadfast.base_url', 'https://portal.steadfast.com.bd');
        $this->apiKey = config('services.steadfast.api_key');
        $this->secretKey = config('services.steadfast.secret_key');
    }

    /**
     * Create order in Steadfast system
     */
    public function createOrder(Order $order)
    {
        try {
            $payload = $this->prepareOrderPayload($order);
            
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/v1/create_order', $payload);

            $responseData = $response->json();

            // Create courier integration record
            $courierIntegration = CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'steadfast',
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

            if ($response->successful() && isset($responseData['consignment_id'])) {
                $courierIntegration->update([
                    'tracking_number' => $responseData['tracking_code'] ?? null,
                    'consignment_id' => $responseData['consignment_id'],
                ]);

                // Update order with tracking info
                $order->update([
                    'notes' => 'Steadfast Courier: ' . $responseData['tracking_code'] . ' (Consignment: ' . $responseData['consignment_id'] . ')'
                ]);

                return [
                    'success' => true,
                    'tracking_number' => $responseData['tracking_code'],
                    'consignment_id' => $responseData['consignment_id'],
                    'message' => 'Order successfully created in Steadfast system'
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to create order in Steadfast',
                'errors' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Steadfast API Error: ' . $e->getMessage());
            
            // Create failed integration record
            CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'steadfast',
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
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
            ])->get($this->baseUrl . '/api/v1/track_order/' . $trackingCode);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Steadfast Tracking Error: ' . $e->getMessage());
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
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/v1/cancel_order', [
                'consignment_id' => $consignmentId
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                // Update integration record
                CourierIntegration::where('courier_type', 'steadfast')
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
            Log::error('Steadfast Cancel Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Prepare order payload for Steadfast API
     */
    private function prepareOrderPayload(Order $order)
    {
        $shippingAddress = $order->shippingAddress;
        
        return [
            'recipient_name' => $shippingAddress->first_name . ' ' . $shippingAddress->last_name,
            'recipient_phone' => $shippingAddress->phone,
            'recipient_address' => $shippingAddress->address . ', ' . 
                                   ($shippingAddress->city ?? '') . ', ' . 
                                   ($shippingAddress->postal_code ?? ''),
            'recipient_city' => $shippingAddress->city ?? 'Dhaka',
            'recipient_zone' => $shippingAddress->city ?? 'Dhaka',
            'recipient_area' => $shippingAddress->city ?? 'Dhaka',
            'pickup_address' => $this->getPickupAddress(),
            'delivery_address' => $shippingAddress->address,
            'package_weight' => $this->calculatePackageWeight($order),
            'package_description' => $this->getPackageDescription($order),
            'cod_amount' => $order->payment && $order->payment->method === 'cod' ? $order->total_amount : 0,
            'instruction' => $order->notes ?? 'Handle with care',
            'value' => $order->total_amount,
        ];
    }

    /**
     * Get pickup address
     */
    private function getPickupAddress()
    {
        return config('services.steadfast.pickup_address', 'Shop Address, Dhaka');
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
