<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookController extends Controller
{
    /**
     * Handle Facebook Conversions API requests
     */
    public function conversions(Request $request)
    {
        try {
            $data = $request->all();
            
            // Facebook Conversions API configuration
            $accessToken = config('services.facebook.access_token');
            $pixelId = config('services.facebook.pixel_id');
            
            if (!$accessToken || !$pixelId) {
                Log::warning('Facebook Conversions API: Missing credentials');
                return response()->json(['success' => false, 'message' => 'Facebook API not configured'], 400);
            }
            
            // Prepare the event data for Facebook Conversions API
            $eventData = [
                'data' => [
                    [
                        'event_name' => $data['event_name'] ?? 'Purchase',
                        'event_time' => $data['event_time'] ?? time(),
                        'action_source' => 'website',
                        'user_data' => $data['user_data'] ?? [],
                        'custom_data' => $data['custom_data'] ?? []
                    ]
                ],
                'test_event_code' => config('app.debug') ? 'TEST12345' : null
            ];
            
            // Send to Facebook Conversions API
            $response = Http::post("https://graph.facebook.com/v18.0/{$pixelId}/events", $eventData, [
                'access_token' => $accessToken
            ]);
            
            if ($response->successful()) {
                Log::info('Facebook Conversions API: Event sent successfully', [
                    'event' => $data['event_name'] ?? 'Unknown',
                    'response' => $response->json()
                ]);
                
                return response()->json(['success' => true, 'message' => 'Event sent successfully']);
            } else {
                Log::error('Facebook Conversions API: Failed to send event', [
                    'event' => $data['event_name'] ?? 'Unknown',
                    'response' => $response->json(),
                    'status' => $response->status()
                ]);
                
                return response()->json(['success' => false, 'message' => 'Failed to send event'], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Facebook Conversions API: Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
    
    /**
     * Get Facebook Pixel configuration
     */
    public function pixelConfig()
    {
        return response()->json([
            'pixel_id' => config('services.facebook.pixel_id'),
            'enabled' => config('services.facebook.enabled', false)
        ]);
    }
}
