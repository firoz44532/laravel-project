<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NetworkOptimizationController extends Controller
{
    /**
     * Get network-optimized asset URLs
     */
    public function getOptimizedAssets(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $networkInfo = $this->detectNetworkCapabilities($request);
        
        return response()->json([
            'network_type' => $networkInfo['type'],
            'optimization_level' => $networkInfo['optimization_level'],
            'image_quality' => $networkInfo['image_quality'],
            'video_quality' => $networkInfo['video_quality'],
            'enable_animations' => $networkInfo['enable_animations'],
            'preload_strategy' => $networkInfo['preload_strategy'],
            'cache_duration' => $networkInfo['cache_duration']
        ]);
    }
    
    /**
     * Detect network capabilities and determine optimization level
     */
    private function detectNetworkCapabilities(Request $request)
    {
        // Get client hints for network information
        $downlink = $request->header('Sec-CH-Downlink'); // Mbps
        $rtt = $request->header('Sec-CH-RTT'); // Round-trip time in ms
        $effectiveType = $request->header('Sec-CH-Effective-Connection-Type');
        $saveData = $request->header('Sec-CH-Save-Data'); // Data saver mode
        
        // 5G detection logic
        if ($downlink && $downlink >= 10 && $rtt && $rtt <= 50) {
            return [
                'type' => '5g-excellent',
                'optimization_level' => 'maximum',
                'image_quality' => 'ultra-hd',
                'video_quality' => '4k',
                'enable_animations' => true,
                'preload_strategy' => 'aggressive',
                'cache_duration' => 31536000 // 1 year
            ];
        } elseif ($downlink && $downlink >= 5 && $rtt && $rtt <= 100) {
            return [
                'type' => '5g-good',
                'optimization_level' => 'high',
                'image_quality' => 'high',
                'video_quality' => '1080p',
                'enable_animations' => true,
                'preload_strategy' => 'moderate',
                'cache_duration' => 2592000 // 30 days
            ];
        } elseif ($effectiveType === '4g') {
            return [
                'type' => '4g-standard',
                'optimization_level' => 'standard',
                'image_quality' => 'medium',
                'video_quality' => '720p',
                'enable_animations' => true,
                'preload_strategy' => 'conservative',
                'cache_duration' => 604800 // 7 days
            ];
        } elseif ($effectiveType === '3g') {
            return [
                'type' => '3g-slow',
                'optimization_level' => 'reduced',
                'image_quality' => 'low',
                'video_quality' => '480p',
                'enable_animations' => false,
                'preload_strategy' => 'minimal',
                'cache_duration' => 86400 // 1 day
            ];
        } else {
            return [
                'type' => '2g-very-slow',
                'optimization_level' => 'minimal',
                'image_quality' => 'very-low',
                'video_quality' => '360p',
                'enable_animations' => false,
                'preload_strategy' => 'disabled',
                'cache_duration' => 3600 // 1 hour
            ];
        }
    }
    
    /**
     * Serve network-optimized images
     */
    public function serveOptimizedImage(Request $request, $path, $quality = 'auto')
    {
        $networkInfo = $this->detectNetworkCapabilities($request);
        $imageQuality = $quality === 'auto' ? $networkInfo['image_quality'] : $quality;
        
        // Determine image dimensions based on network
        $dimensions = $this->getImageDimensions($networkInfo['optimization_level']);
        
        // Apply optimization based on network capabilities
        $imagePath = storage_path('app/public/' . $path);
        
        if (!file_exists($imagePath)) {
            abort(404);
        }
        
        $image = \Intervention\Image\Facades\Image::make($imagePath);
        
        // Resize based on network optimization level
        if ($dimensions['width'] && $image->width() > $dimensions['width']) {
            $image->resize($dimensions['width'], null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        if ($dimensions['height'] && $image->height() > $dimensions['height']) {
            $image->resize(null, $dimensions['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Set quality based on network
        $qualityLevel = $this->getCompressionQuality($imageQuality);
        $image->encode('jpg', $qualityLevel);
        
        return response($image->getEncoded())
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=' . $networkInfo['cache_duration'])
            ->header('X-Network-Optimization', $networkInfo['type']);
    }
    
    /**
     * Get image dimensions based on optimization level
     */
    private function getImageDimensions($optimizationLevel)
    {
        $dimensions = [
            'maximum' => ['width' => 1920, 'height' => 1080],
            'high' => ['width' => 1280, 'height' => 720],
            'standard' => ['width' => 800, 'height' => 600],
            'reduced' => ['width' => 400, 'height' => 300],
            'minimal' => ['width' => 200, 'height' => 150]
        ];
        
        return $dimensions[$optimizationLevel] ?? $dimensions['standard'];
    }
    
    /**
     * Get compression quality based on image quality setting
     */
    private function getCompressionQuality($imageQuality)
    {
        $qualities = [
            'ultra-hd' => 95,
            'high' => 85,
            'medium' => 75,
            'low' => 60,
            'very-low' => 40
        ];
        
        return $qualities[$imageQuality] ?? 75;
    }
    
    /**
     * Get 5G-specific performance metrics
     */
    public function get5GMetrics(Request $request)
    {
        return response()->json([
            'target_load_time' => '< 1000ms',
            'target_lcp' => '< 1.2s',
            'target_fid' => '< 100ms',
            'target_cls' => '< 0.1',
            'supported_features' => [
                'real_time_updates' => true,
                'hd_video_streaming' => true,
                'instant_preloading' => true,
                'predictive_caching' => true,
                'enhanced_animations' => true,
                'webp_images' => true,
                'http2_push' => true
            ],
            'optimization_applied' => [
                'aggressive_preloading',
                'hd_image_quality',
                'minimal_compression',
                'enhanced_caching',
                'real_time_features'
            ]
        ]);
    }
}
