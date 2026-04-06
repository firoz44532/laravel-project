<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageOptimizationController extends Controller
{
    /**
     * Serve optimized images with proper caching
     */
    public function serve($path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            
            if (!file_exists($fullPath)) {
                abort(404);
            }
            
            $image = Image::make($fullPath);
            
            // Get image dimensions
            $width = $image->width();
            $height = $image->height();
            
            // Optimize for web
            if ($width > 1200) {
                $image->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            if ($height > 800) {
                $image->resize(null, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Optimize quality
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            
            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $image->encode('jpg', 85);
                    $mimeType = 'image/jpeg';
                    break;
                case 'png':
                    $image->encode('png', 8);
                    $mimeType = 'image/png';
                    break;
                case 'webp':
                    $image->encode('webp', 85);
                    $mimeType = 'image/webp';
                    break;
                default:
                    $mimeType = mime_content_type($fullPath);
            }
            
            // Cache headers
            $response = response($image->getEncoded())
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000))
                ->header('Last-Modified', gmdate('D, d M Y H:i:s \G\M\T', filemtime($fullPath)));
            
            return $response;
            
        } catch (\Exception $e) {
            abort(404);
        }
    }
    
    /**
     * Generate WebP versions of images
     */
    public function webp($path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            
            if (!file_exists($fullPath)) {
                abort(404);
            }
            
            $image = Image::make($fullPath);
            
            // Convert to WebP with optimization
            $image->encode('webp', 85);
            
            return response($image->getEncoded())
                ->header('Content-Type', 'image/webp')
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Vary', 'Accept');
                
        } catch (\Exception $e) {
            abort(404);
        }
    }
    
    /**
     * Generate thumbnails for products
     */
    public function thumbnail($path, $size = '300x300')
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            
            if (!file_exists($fullPath)) {
                abort(404);
            }
            
            // Parse size
            $dimensions = explode('x', $size);
            $width = isset($dimensions[0]) ? (int)$dimensions[0] : 300;
            $height = isset($dimensions[1]) ? (int)$dimensions[1] : 300;
            
            $image = Image::make($fullPath);
            
            // Create thumbnail
            $image->fit($width, $height, function ($constraint) {
                $constraint->upsize();
            });
            
            // Optimize for web
            $image->encode('jpg', 85);
            
            return response($image->getEncoded())
                ->header('Content-Type', 'image/jpeg')
                ->header('Cache-Control', 'public, max-age=31536000');
                
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
