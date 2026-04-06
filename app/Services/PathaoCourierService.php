<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CourierIntegration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoCourierService
{
    protected $baseUrl;
    protected $clientEmail;
    protected $clientPassword;
    protected $clientSecret;
    protected $accessToken;

    public function __construct()
    {
        $this->baseUrl = config('services.pathao.base_url', 'https://api-hermes.pathao.com');
        $this->clientEmail = config('services.pathao.client_email');
        $this->clientPassword = config('services.pathao.client_password');
        $this->clientSecret = config('services.pathao.client_secret');
    }

    /**
     * Get access token
     */
    private function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = Http::post($this->baseUrl . '/v1/issue-token', [
                'client_email' => $this->clientEmail,
                'client_password' => $this->clientPassword,
                'client_secret' => $this->clientSecret,
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['data']['access_token'])) {
                $this->accessToken = $data['data']['access_token'];
                return $this->accessToken;
            }

            throw new \Exception('Failed to get access token: ' . ($data['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('Pathao Auth Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create order in Pathao system
     */
    public function createOrder(Order $order)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            // Get store info
            $storeInfo = $this->getStoreInfo($accessToken);
            if (!$storeInfo['success']) {
                throw new \Exception('Failed to get store info');
            }

            // Get city and zone info
            $cityInfo = $this->getCityZoneInfo($accessToken, $order->shippingAddress->city ?? 'Dhaka');
            if (!$cityInfo['success']) {
                throw new \Exception('Failed to get city/zone info');
            }

            $payload = $this->prepareOrderPayload($order, $storeInfo['store_id'], $cityInfo);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/v1/orders', $payload);

            $responseData = $response->json();

            // Create courier integration record
            $courierIntegration = CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'pathao',
                'status' => $response->successful() ? 'synced' : 'failed',
                'pickup_address' => $payload['pickup_address'],
                'delivery_address' => $payload['delivery_address'],
                'customer_name' => $payload['recipient_name'],
                'customer_phone' => $payload['recipient_phone'],
                'package_weight' => $payload['package_weight'] ?? 0.5,
                'package_description' => $payload['package_description'],
                'cod_amount' => $payload['cod_amount'] ?? 0,
                'delivery_charge' => $responseData['data']['delivery_fee'] ?? 0,
                'api_response' => $responseData,
                'error_message' => $response->successful() ? null : ($responseData['message'] ?? 'Unknown error'),
                'synced_at' => $response->successful() ? now() : null,
            ]);

            if ($response->successful() && isset($responseData['data']['consignment_id'])) {
                $courierIntegration->update([
                    'tracking_number' => $responseData['data']['tracking_code'] ?? null,
                    'consignment_id' => $responseData['data']['consignment_id'],
                ]);

                // Update order with tracking info
                $order->update([
                    'notes' => 'Pathao Courier: ' . $responseData['data']['tracking_code'] . ' (Consignment: ' . $responseData['data']['consignment_id'] . ')'
                ]);

                return [
                    'success' => true,
                    'tracking_number' => $responseData['data']['tracking_code'],
                    'consignment_id' => $responseData['data']['consignment_id'],
                    'message' => 'Order successfully created in Pathao system'
                ];
            }

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to create order in Pathao',
                'errors' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Pathao API Error: ' . $e->getMessage());
            
            // Create failed integration record
            CourierIntegration::create([
                'order_id' => $order->id,
                'courier_type' => 'pathao',
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
            $accessToken = $this->getAccessToken();
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->baseUrl . '/v1/orders/track/' . $trackingCode);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Pathao Tracking Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder($consignmentId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/orders/cancel', [
                'consignment_id' => $consignmentId
            ]);

            $responseData = $response->json();

            if ($response->successful()) {
                // Update integration record
                CourierIntegration::where('courier_type', 'pathao')
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
            Log::error('Pathao Cancel Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get store info
     */
    private function getStoreInfo($accessToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->baseUrl . '/v1/stores');

            $data = $response->json();

            if ($response->successful() && isset($data['data']['stores'][0])) {
                return [
                    'success' => true,
                    'store_id' => $data['data']['stores'][0]['store_id']
                ];
            }

            return ['success' => false, 'message' => 'No store found'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get city and zone info
     */
    private function getCityZoneInfo($accessToken, $cityName)
    {
        try {
            // Get cities
            $citiesResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->baseUrl . '/v1/cities');

            $citiesData = $citiesResponse->json();

            if (!$citiesResponse->successful()) {
                return ['success' => false, 'message' => 'Failed to get cities'];
            }

            // Find city ID (default to Dhaka if not found)
            $cityId = null;
            foreach ($citiesData['data']['data'] as $city) {
                if (stripos($city['city_name'], $cityName) !== false) {
                    $cityId = $city['city_id'];
                    break;
                }
            }

            if (!$cityId) {
                // Default to Dhaka
                foreach ($citiesData['data']['data'] as $city) {
                    if (stripos($city['city_name'], 'Dhaka') !== false) {
                        $cityId = $city['city_id'];
                        break;
                    }
                }
            }

            if (!$cityId) {
                return ['success' => false, 'message' => 'City not found'];
            }

            // Get zones for the city
            $zonesResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->baseUrl . '/v1/cities/' . $cityId . '/zone-list');

            $zonesData = $zonesResponse->json();

            if (!$zonesResponse->successful() || empty($zonesData['data']['data'])) {
                return ['success' => false, 'message' => 'No zones found for city'];
            }

            $zoneId = $zonesData['data']['data'][0]['zone_id'];

            return [
                'success' => true,
                'city_id' => $cityId,
                'zone_id' => $zoneId
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Prepare order payload for Pathao API
     */
    private function prepareOrderPayload(Order $order, $storeId, $cityZoneInfo)
    {
        $shippingAddress = $order->shippingAddress;
        
        return [
            'store_id' => $storeId,
            'recipient_name' => $shippingAddress->first_name . ' ' . $shippingAddress->last_name,
            'recipient_phone' => $shippingAddress->phone,
            'recipient_address' => $shippingAddress->address,
            'recipient_city' => $cityZoneInfo['city_id'],
            'recipient_zone' => $cityZoneInfo['zone_id'],
            'delivery_type' => 48, // 48 = Normal delivery
            'item_type' => 2, // 2 = Parcel
            'special_instruction' => $order->notes ?? 'Handle with care',
            'item_quantity' => $order->items->count(),
            'item_weight' => $this->calculatePackageWeight($order),
            'amount_to_collect' => $order->payment && $order->payment->method === 'cod' ? $order->total_amount : 0,
            'item_description' => $this->getPackageDescription($order),
        ];
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
