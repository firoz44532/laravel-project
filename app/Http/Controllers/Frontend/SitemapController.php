<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Blog;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = $this->generateSitemap();
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function products()
    {
        $sitemap = $this->generateProductsSitemap();
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function categories()
    {
        $sitemap = $this->generateCategoriesSitemap();
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function brands()
    {
        $sitemap = $this->generateBrandsSitemap();
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function blog()
    {
        $sitemap = $this->generateBlogSitemap();
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function sitemapIndex()
    {
        $sitemap = $this->generateSitemapIndex();
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    private function generateSitemapIndex()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Main sitemap
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap.xml') . '</loc>';
        $xml .= '<lastmod>' . Carbon::now()->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        $xml .= '</sitemap>';
        
        // Products sitemap
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-products.xml') . '</loc>';
        $xml .= '<lastmod>' . Carbon::now()->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        $xml .= '</sitemap>';
        
        // Categories sitemap
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-categories.xml') . '</loc>';
        $xml .= '<lastmod>' . Carbon::now()->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        $xml .= '</sitemap>';
        
        // Brands sitemap
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-brands.xml') . '</loc>';
        $xml .= '<lastmod>' . Carbon::now()->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        $xml .= '</sitemap>';
        
        // Blog sitemap
        $xml .= '<sitemap>';
        $xml .= '<loc>' . url('sitemap-blog.xml') . '</loc>';
        $xml .= '<lastmod>' . Carbon::now()->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        $xml .= '</sitemap>';
        
        $xml .= '</sitemapindex>';
        
        return $xml;
    }

    private function generateSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        $xml .= $this->generateUrlEntry(
            url('/'),
            Carbon::now(),
            '1.0',
            'daily'
        );
        
        // Static pages
        $staticPages = [
            'about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            'contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            'faq' => ['priority' => '0.6', 'changefreq' => 'monthly'],
            'terms' => ['priority' => '0.5', 'changefreq' => 'yearly'],
            'privacy' => ['priority' => '0.5', 'changefreq' => 'yearly'],
            'wishlist' => ['priority' => '0.6', 'changefreq' => 'weekly'],
            'cart' => ['priority' => '0.4', 'changefreq' => 'daily'],
            'checkout' => ['priority' => '0.3', 'changefreq' => 'daily'],
            'comparison' => ['priority' => '0.5', 'changefreq' => 'weekly'],
            'tracking' => ['priority' => '0.4', 'changefreq' => 'daily'],
        ];
        
        foreach ($staticPages as $page => $data) {
            $xml .= $this->generateUrlEntry(
                url($page),
                Carbon::now()->subDays(7),
                $data['priority'],
                $data['changefreq']
            );
        }
        
        // Categories (limited to main sitemap)
        $categories = Category::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
            
        foreach ($categories as $category) {
            $xml .= $this->generateUrlEntry(
                route('categories.show', $category->slug),
                $category->updated_at ?? $category->created_at,
                '0.8',
                'weekly'
            );
        }
        
        // Brands (limited to main sitemap)
        $brands = Brand::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
            
        foreach ($brands as $brand) {
            $xml .= $this->generateUrlEntry(
                route('brands.show', $brand->slug),
                $brand->updated_at ?? $brand->created_at,
                '0.7',
                'weekly'
            );
        }
        
        // Products (limited to main sitemap)
        $products = Product::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get();
            
        foreach ($products as $product) {
            $xml .= $this->generateUrlEntry(
                route('products.show', $product->slug),
                $product->updated_at ?? $product->created_at,
                '0.9',
                'daily'
            );
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function generateProductsSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $products = Product::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($products as $product) {
            $xml .= $this->generateUrlEntry(
                route('products.show', $product->slug),
                $product->updated_at ?? $product->created_at,
                '0.9',
                'daily'
            );
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function generateCategoriesSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $categories = Category::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($categories as $category) {
            $xml .= $this->generateUrlEntry(
                route('categories.show', $category->slug),
                $category->updated_at ?? $category->created_at,
                '0.8',
                'weekly'
            );
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function generateBrandsSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $brands = Brand::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($brands as $brand) {
            $xml .= $this->generateUrlEntry(
                route('brands.show', $brand->slug),
                $brand->updated_at ?? $brand->created_at,
                '0.7',
                'weekly'
            );
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function generateBlogSitemap()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Blog index
        $xml .= $this->generateUrlEntry(
            url('/blog'),
            Carbon::now()->subDays(7),
            '0.6',
            'weekly'
        );
        
        // Blog posts (if blog model exists)
        if (class_exists('App\Models\Blog')) {
            $blogs = Blog::where('is_active', true)
                ->orderBy('updated_at', 'desc')
                ->get();
                
            foreach ($blogs as $blog) {
                $xml .= $this->generateUrlEntry(
                    route('blog.show', $blog->slug),
                    $blog->updated_at ?? $blog->created_at,
                    '0.7',
                    'weekly'
                );
            }
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }

    private function generateUrlEntry($url, $lastmod, $priority, $changefreq)
    {
        $xml = '<url>';
        $xml .= '<loc>' . $url . '</loc>';
        $xml .= '<lastmod>' . $lastmod->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '</url>';
        
        return $xml;
    }

    public function robots()
    {
        $robots = 'User-agent: *' . "\n";
        $robots .= 'Allow: /' . "\n";
        $robots .= 'Disallow: /admin/' . "\n";
        $robots .= 'Disallow: /cart/' . "\n";
        $robots .= 'Disallow: /checkout/' . "\n";
        $robots .= 'Disallow: /api/' . "\n";
        $robots .= 'Disallow: /storage/' . "\n";
        $robots .= 'Disallow: /vendor/' . "\n";
        $robots .= 'Disallow: /*.json$' . "\n";
        $robots .= 'Disallow: /*.php$' . "\n";
        $robots .= 'Disallow: /*.env' . "\n";
        $robots .= 'Disallow: /*.log' . "\n";
        $robots .= 'Allow: /sitemap.xml' . "\n";
        $robots .= 'Allow: /sitemap-products.xml' . "\n";
        $robots .= 'Allow: /sitemap-categories.xml' . "\n";
        $robots .= 'Allow: /sitemap-brands.xml' . "\n";
        $robots .= 'Allow: /sitemap-blog.xml' . "\n";
        $robots .= 'Sitemap: ' . url('sitemap.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-products.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-categories.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-brands.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-blog.xml') . "\n";
        
        return response($robots)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    public function generateAllSitemaps()
    {
        // Generate all sitemaps and save to public directory
        $sitemaps = [
            'sitemap.xml' => $this->generateSitemap(),
            'sitemap-products.xml' => $this->generateProductsSitemap(),
            'sitemap-categories.xml' => $this->generateCategoriesSitemap(),
            'sitemap-brands.xml' => $this->generateBrandsSitemap(),
            'sitemap-blog.xml' => $this->generateBlogSitemap(),
            'robots.txt' => $this->generateRobots(),
        ];

        $results = [];
        
        foreach ($sitemaps as $filename => $content) {
            $path = public_path($filename);
            file_put_contents($path, $content);
            $results[] = "Generated: {$filename}";
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    private function generateRobots()
    {
        $robots = 'User-agent: *' . "\n";
        $robots .= 'Allow: /' . "\n";
        $robots .= 'Disallow: /admin/' . "\n";
        $robots .= 'Disallow: /cart/' . "\n";
        $robots .= 'Disallow: /checkout/' . "\n";
        $robots .= 'Disallow: /api/' . "\n";
        $robots .= 'Disallow: /storage/' . "\n";
        $robots .= 'Disallow: /vendor/' . "\n";
        $robots .= 'Disallow: /*.json$' . "\n";
        $robots .= 'Disallow: /*.php$' . "\n";
        $robots .= 'Disallow: /*.env' . "\n";
        $robots .= 'Disallow: /*.log' . "\n";
        $robots .= 'Allow: /sitemap.xml' . "\n";
        $robots .= 'Allow: /sitemap-products.xml' . "\n";
        $robots .= 'Allow: /sitemap-categories.xml' . "\n";
        $robots .= 'Allow: /sitemap-brands.xml' . "\n";
        $robots .= 'Allow: /sitemap-blog.xml' . "\n";
        $robots .= 'Sitemap: ' . url('sitemap.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-products.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-categories.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-brands.xml') . "\n";
        $robots .= 'Sitemap: ' . url('sitemap-blog.xml') . "\n";
        
        return $robots;
    }

    public function pingSearchEngines()
    {
        $sitemapUrl = url('sitemap.xml');
        $searchEngines = [
            'Google' => 'http://www.google.com/webmasters/tools/ping?sitemap=' . $sitemapUrl,
            'Bing' => 'http://www.bing.com/webmaster/ping.aspx?siteMap=' . $sitemapUrl,
            'Yandex' => 'http://webmaster.yandex.ru/ping.xml?sitemap=' . $sitemapUrl,
        ];

        $results = [];
        
        foreach ($searchEngines as $engine => $url) {
            try {
                $response = file_get_contents($url);
                $results[$engine] = 'Success';
            } catch (\Exception $e) {
                $results[$engine] = 'Failed: ' . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
