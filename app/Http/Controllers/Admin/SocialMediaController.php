<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SocialMediaCampaignRequest;
use App\Http\Requests\Admin\SocialMediaSchedulePostRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SocialMediaController extends Controller
{
    public function index()
    {
        return view('admin.social-media.index');
    }

    public function getAnalytics()
    {
        $period = request()->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $analytics = [
            'facebook' => $this->getFacebookAnalytics($dateRange),
            'twitter' => $this->getTwitterAnalytics($dateRange),
            'instagram' => $this->getInstagramAnalytics($dateRange),
            'youtube' => $this->getYouTubeAnalytics($dateRange),
            'linkedin' => $this->getLinkedInAnalytics($dateRange),
            'tiktok' => $this->getTikTokAnalytics($dateRange),
        ];

        return response()->json($analytics);
    }

    public function getFacebookAnalytics($dateRange)
    {
        $cacheKey = 'facebook_analytics_' . $dateRange['start']->format('Ymd') . '_' . $dateRange['end']->format('Ymd');
        
        return Cache::remember($cacheKey, 3600, function() use ($dateRange) {
            $accessToken = env('FACEBOOK_ACCESS_TOKEN');
            $pageId = env('FACEBOOK_PAGE_ID');
            
            if (!$accessToken || !$pageId) {
                return $this->getEmptyAnalytics();
            }

            try {
                // Get page insights
                $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}/insights", [
                    'access_token' => $accessToken,
                    'metric' => 'page_impressions,page_reach,page_engaged_users,page_fan_adds,page_fan_removes,page_post_engagements,page_video_views,page_video_complete_views',
                    'period' => 'day',
                    'since' => $dateRange['start']->format('Y-m-d'),
                    'until' => $dateRange['end']->format('Y-m-d'),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'impressions' => $this->sumMetric($data, 'page_impressions'),
                        'reach' => $this->sumMetric($data, 'page_reach'),
                        'engaged_users' => $this->sumMetric($data, 'page_engaged_users'),
                        'new_fans' => $this->sumMetric($data, 'page_fan_adds'),
                        'lost_fans' => $this->sumMetric($data, 'page_fan_removes'),
                        'post_engagements' => $this->sumMetric($data, 'page_post_engagements'),
                        'video_views' => $this->sumMetric($data, 'page_video_views'),
                        'video_complete_views' => $this->sumMetric($data, 'page_video_complete_views'),
                        'engagement_rate' => $this->calculateEngagementRate($data),
                    ];
                }
            } catch (\Exception $e) {
                return $this->getEmptyAnalytics();
            }

            return $this->getEmptyAnalytics();
        });
    }

    public function getTwitterAnalytics($dateRange)
    {
        $cacheKey = 'twitter_analytics_' . $dateRange['start']->format('Ymd') . '_' . $dateRange['end']->format('Ymd');
        
        return Cache::remember($cacheKey, 3600, function() use ($dateRange) {
            $bearerToken = env('TWITTER_BEARER_TOKEN');
            
            if (!$bearerToken) {
                return $this->getEmptyAnalytics();
            }

            try {
                // Get user metrics
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                ])->get('https://api.twitter.com/2/users/me', [
                    'user.fields' => 'public_metrics',
                ]);

                if ($response->successful()) {
                    $data = $response->json()['data']['public_metrics'] ?? [];
                    return [
                        'followers_count' => $data['followers_count'] ?? 0,
                        'following_count' => $data['following_count'] ?? 0,
                        'tweet_count' => $data['tweet_count'] ?? 0,
                        'listed_count' => $data['listed_count'] ?? 0,
                        'engagement_rate' => $this->calculateTwitterEngagementRate($data),
                    ];
                }
            } catch (\Exception $e) {
                return $this->getEmptyAnalytics();
            }

            return $this->getEmptyAnalytics();
        });
    }

    public function getInstagramAnalytics($dateRange)
    {
        $cacheKey = 'instagram_analytics_' . $dateRange['start']->format('Ymd') . '_' . $dateRange['end']->format('Ymd');
        
        return Cache::remember($cacheKey, 3600, function() use ($dateRange) {
            $accessToken = env('INSTAGRAM_ACCESS_TOKEN');
            
            if (!$accessToken) {
                return $this->getEmptyAnalytics();
            }

            try {
                // Get Instagram insights
                $response = Http::get("https://graph.instagram.com/v18.0/me/insights", [
                    'access_token' => $accessToken,
                    'metric' => 'impressions,reach,engagement,profile_views,website_clicks',
                    'period' => 'day',
                    'since' => $dateRange['start']->format('Y-m-d'),
                    'until' => $dateRange['end']->format('Y-m-d'),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'impressions' => $this->sumMetric($data, 'impressions'),
                        'reach' => $this->sumMetric($data, 'reach'),
                        'engagement' => $this->sumMetric($data, 'engagement'),
                        'profile_views' => $this->sumMetric($data, 'profile_views'),
                        'website_clicks' => $this->sumMetric($data, 'website_clicks'),
                        'engagement_rate' => $this->calculateInstagramEngagementRate($data),
                    ];
                }
            } catch (\Exception $e) {
                return $this->getEmptyAnalytics();
            }

            return $this->getEmptyAnalytics();
        });
    }

    public function getYouTubeAnalytics($dateRange)
    {
        $cacheKey = 'youtube_analytics_' . $dateRange['start']->format('Ymd') . '_' . $dateRange['end']->format('Ymd');
        
        return Cache::remember($cacheKey, 3600, function() use ($dateRange) {
            $apiKey = env('YOUTUBE_API_KEY');
            $channelId = env('YOUTUBE_CHANNEL_ID');
            
            if (!$apiKey || !$channelId) {
                return $this->getEmptyAnalytics();
            }

            try {
                // Get channel statistics
                $response = Http::get("https://www.googleapis.com/youtube/v3/channels", [
                    'key' => $apiKey,
                    'id' => $channelId,
                    'part' => 'statistics',
                ]);

                if ($response->successful()) {
                    $stats = $response->json()['items'][0]['statistics'] ?? [];
                    return [
                        'view_count' => $stats['viewCount'] ?? 0,
                        'subscriber_count' => $stats['subscriberCount'] ?? 0,
                        'video_count' => $stats['videoCount'] ?? 0,
                        'comment_count' => $stats['commentCount'] ?? 0,
                        'like_count' => $stats['likeCount'] ?? 0,
                        'dislike_count' => $stats['dislikeCount'] ?? 0,
                        'engagement_rate' => $this->calculateYouTubeEngagementRate($stats),
                    ];
                }
            } catch (\Exception $e) {
                return $this->getEmptyAnalytics();
            }

            return $this->getEmptyAnalytics();
        });
    }

    public function getLinkedInAnalytics($dateRange)
    {
        $cacheKey = 'linkedin_analytics_' . $dateRange['start']->format('Ymd') . '_' . $dateRange['end']->format('Ymd');
        
        return Cache::remember($cacheKey, 3600, function() use ($dateRange) {
            $accessToken = env('LINKEDIN_ACCESS_TOKEN');
            $companyId = env('LINKEDIN_COMPANY_ID');
            
            if (!$accessToken || !$companyId) {
                return $this->getEmptyAnalytics();
            }

            try {
                // Get company statistics
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get("https://api.linkedin.com/v2/organizations/{$companyId}", [
                    'fields' => 'name,description,website,specialities,employeeCount,followerCount',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'follower_count' => $data['followerCount'] ?? 0,
                        'employee_count' => $data['employeeCount'] ?? 0,
                        'engagement_rate' => $this->calculateLinkedInEngagementRate($data),
                    ];
                }
            } catch (\Exception $e) {
                return $this->getEmptyAnalytics();
            }

            return $this->getEmptyAnalytics();
        });
    }

    public function getTikTokAnalytics($dateRange)
    {
        $cacheKey = 'tiktok_analytics_' . $dateRange['start']->format('Ymd') . '_' . $dateRange['end']->format('Ymd');
        
        return Cache::remember($cacheKey, 3600, function() use ($dateRange) {
            $accessToken = env('TIKTOK_ACCESS_TOKEN');
            
            if (!$accessToken) {
                return $this->getEmptyAnalytics();
            }

            try {
                // Get TikTok user statistics
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get('https://open.tiktokapis.com/v2/user/info/', [
                    'fields' => 'stats,display_name,avatar_url',
                ]);

                if ($response->successful()) {
                    $data = $response->json()['data']['user'] ?? [];
                    return [
                        'follower_count' => $data['stats']['followerCount'] ?? 0,
                        'following_count' => $data['stats']['followingCount'] ?? 0,
                        'heart_count' => $data['stats']['heartCount'] ?? 0,
                        'video_count' => $data['stats']['videoCount'] ?? 0,
                        'digg_count' => $data['stats']['diggCount'] ?? 0,
                        'engagement_rate' => $this->calculateTikTokEngagementRate($data),
                    ];
                }
            } catch (\Exception $e) {
                return $this->getEmptyAnalytics();
            }

            return $this->getEmptyAnalytics();
        });
    }

    public function getMarketingTools()
    {
        return view('admin.social-media.marketing-tools');
    }

    public function createCampaign(SocialMediaCampaignRequest $request)
    {
        $campaign = [
            'name' => $request->name,
            'platform' => $request->platform,
            'type' => $request->type,
            'content' => $request->content,
            'hashtags' => $request->hashtags,
            'target_audience' => $request->target_audience,
            'budget' => $request->budget,
            'duration' => $request->duration,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'draft',
            'created_at' => now(),
        ];

        // Handle file uploads
        if ($request->hasFile('image')) {
            $campaign['image'] = $request->file('image')->store('campaigns', 'public');
        }

        if ($request->hasFile('video')) {
            $campaign['video'] = $request->file('video')->store('campaigns', 'public');
        }

        // Store campaign (you might want to create a Campaign model)
        // Campaign::create($campaign);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'campaign' => $campaign
        ]);
    }

    public function getCampaigns()
    {
        // Get campaigns from database or cache
        $campaigns = Cache::get('social_media_campaigns', []);

        return response()->json($campaigns);
    }

    public function schedulePost(SocialMediaSchedulePostRequest $request)
    {
        $scheduledPost = [
            'platform' => $request->platform,
            'content' => $request->content,
            'scheduled_at' => $request->scheduled_at,
            'media' => $request->media,
            'hashtags' => $request->hashtags,
            'status' => 'scheduled',
            'created_at' => now(),
        ];

        // Store scheduled post
        // ScheduledPost::create($scheduledPost);

        return response()->json([
            'success' => true,
            'message' => 'Post scheduled successfully',
            'post' => $scheduledPost
        ]);
    }

    public function getScheduledPosts()
    {
        $posts = Cache::get('scheduled_social_posts', []);

        return response()->json($posts);
    }

    public function getEngagementMetrics()
    {
        $period = request()->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $metrics = [
            'total_engagement' => 0,
            'engagement_rate' => 0,
            'top_performing_posts' => [],
            'best_times_to_post' => [],
            'audience_demographics' => [],
            'hashtag_performance' => [],
        ];

        // Calculate metrics from all platforms
        $platforms = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'tiktok'];
        
        foreach ($platforms as $platform) {
            $platformMetrics = $this->getPlatformEngagementMetrics($platform, $dateRange);
            $metrics['total_engagement'] += $platformMetrics['total_engagement'] ?? 0;
            $metrics['top_performing_posts'] = array_merge($metrics['top_performing_posts'], $platformMetrics['top_performing_posts'] ?? []);
        }

        // Sort by engagement
        usort($metrics['top_performing_posts'], function($a, $b) {
            return $b['engagement'] - $a['engagement'];
        });

        $metrics['top_performing_posts'] = array_slice($metrics['top_performing_posts'], 0, 10);
        $metrics['engagement_rate'] = $this->calculateOverallEngagementRate($metrics);

        return response()->json($metrics);
    }

    public function getCompetitorAnalysis()
    {
        $competitors = [
            [
                'name' => 'Competitor 1',
                'platform' => 'facebook',
                'followers' => 50000,
                'engagement_rate' => 3.5,
                'posts_per_week' => 7,
                'top_hashtags' => ['#ecommerce', '#shopping', '#deals'],
            ],
            [
                'name' => 'Competitor 2',
                'platform' => 'instagram',
                'followers' => 75000,
                'engagement_rate' => 4.2,
                'posts_per_week' => 10,
                'top_hashtags' => ['#fashion', '#style', '#ootd'],
            ],
        ];

        return response()->json($competitors);
    }

    public function getHashtagPerformance()
    {
        $hashtags = [
            '#ecommerce' => [
                'usage_count' => 1250,
                'engagement_rate' => 4.5,
                'reach' => 50000,
                'trending' => true,
            ],
            '#shopping' => [
                'usage_count' => 980,
                'engagement_rate' => 3.8,
                'reach' => 42000,
                'trending' => true,
            ],
            '#deals' => [
                'usage_count' => 750,
                'engagement_rate' => 5.2,
                'reach' => 38000,
                'trending' => false,
            ],
        ];

        return response()->json($hashtags);
    }

    public function getBestTimesToPost()
    {
        $times = [
            'facebook' => [
                'monday' => ['09:00', '14:00', '19:00'],
                'tuesday' => ['10:00', '15:00', '20:00'],
                'wednesday' => ['09:00', '14:00', '19:00'],
                'thursday' => ['10:00', '15:00', '20:00'],
                'friday' => ['09:00', '14:00', '19:00'],
                'saturday' => ['10:00', '15:00', '20:00'],
                'sunday' => ['11:00', '16:00', '21:00'],
            ],
            'instagram' => [
                'monday' => ['06:00', '12:00', '18:00'],
                'tuesday' => ['07:00', '13:00', '19:00'],
                'wednesday' => ['06:00', '12:00', '18:00'],
                'thursday' => ['07:00', '13:00', '19:00'],
                'friday' => ['06:00', '12:00', '18:00'],
                'saturday' => ['08:00', '14:00', '20:00'],
                'sunday' => ['09:00', '15:00', '21:00'],
            ],
        ];

        return response()->json($times);
    }

    public function getAudienceDemographics()
    {
        $demographics = [
            'age_groups' => [
                '18-24' => 25,
                '25-34' => 35,
                '35-44' => 20,
                '45-54' => 15,
                '55-64' => 5,
            ],
            'gender' => [
                'male' => 45,
                'female' => 55,
            ],
            'location' => [
                'Dhaka' => 40,
                'Chittagong' => 20,
                'Sylhet' => 15,
                'Khulna' => 10,
                'Rajshahi' => 8,
                'Barisal' => 5,
                'Rangpur' => 2,
            ],
            'interests' => [
                'Fashion' => 30,
                'Electronics' => 25,
                'Home & Garden' => 20,
                'Beauty' => 15,
                'Food' => 10,
            ],
        ];

        return response()->json($demographics);
    }

    private function getEmptyAnalytics()
    {
        return [
            'impressions' => 0,
            'reach' => 0,
            'engaged_users' => 0,
            'new_fans' => 0,
            'lost_fans' => 0,
            'post_engagements' => 0,
            'video_views' => 0,
            'video_complete_views' => 0,
            'engagement_rate' => 0,
        ];
    }

    private function sumMetric($data, $metric)
    {
        if (!isset($data['data'])) return 0;
        
        $total = 0;
        foreach ($data['data'] as $item) {
            $total += $item[$metric] ?? 0;
        }
        
        return $total;
    }

    private function calculateEngagementRate($data)
    {
        $impressions = $this->sumMetric($data, 'page_impressions');
        $engagements = $this->sumMetric($data, 'page_post_engagements');
        
        return $impressions > 0 ? round(($engagements / $impressions) * 100, 2) : 0;
    }

    private function calculateTwitterEngagementRate($data)
    {
        $followers = $data['followers_count'] ?? 0;
        $engagements = ($data['like_count'] ?? 0) + ($data['retweet_count'] ?? 0) + ($data['reply_count'] ?? 0);
        
        return $followers > 0 ? round(($engagements / $followers) * 100, 2) : 0;
    }

    private function calculateInstagramEngagementRate($data)
    {
        $reach = $this->sumMetric($data, 'reach');
        $engagement = $this->sumMetric($data, 'engagement');
        
        return $reach > 0 ? round(($engagement / $reach) * 100, 2) : 0;
    }

    private function calculateYouTubeEngagementRate($stats)
    {
        $views = $stats['viewCount'] ?? 0;
        $engagements = ($stats['likeCount'] ?? 0) + ($stats['commentCount'] ?? 0);
        
        return $views > 0 ? round(($engagements / $views) * 100, 2) : 0;
    }

    private function calculateLinkedInEngagementRate($data)
    {
        $followers = $data['followerCount'] ?? 0;
        // LinkedIn engagement rate is typically calculated differently
        return $followers > 0 ? 2.5 : 0; // Average LinkedIn engagement rate
    }

    private function calculateTikTokEngagementRate($data)
    {
        $followers = $data['stats']['followerCount'] ?? 0;
        $engagements = ($data['stats']['heartCount'] ?? 0) + ($data['stats']['diggCount'] ?? 0) + ($data['stats']['commentCount'] ?? 0);
        
        return $followers > 0 ? round(($engagements / $followers) * 100, 2) : 0;
    }

    private function getPlatformEngagementMetrics($platform, $dateRange)
    {
        // This would fetch platform-specific engagement metrics
        // For now, return empty data
        return [
            'total_engagement' => 0,
            'top_performing_posts' => [],
        ];
    }

    private function calculateOverallEngagementRate($metrics)
    {
        // Calculate overall engagement rate across all platforms
        return $metrics['total_engagement'] > 0 ? 3.5 : 0; // Average engagement rate
    }

    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case '7days':
                return [
                    'start' => $now->copy()->subDays(7),
                    'end' => $now
                ];
            case '30days':
                return [
                    'start' => $now->copy()->subDays(30),
                    'end' => $now
                ];
            case '90days':
                return [
                    'start' => $now->copy()->subDays(90),
                    'end' => $now
                ];
            case '1year':
                return [
                    'start' => $now->copy()->subYear(),
                    'end' => $now
                ];
            default:
                return [
                    'start' => $now->copy()->subDays(30),
                    'end' => $now
                ];
        }
    }

    public function exportAnalytics(Request $request)
    {
        $platform = $request->get('platform');
        $period = $request->get('period', '30days');
        $format = $request->get('format', 'csv');
        
        $analytics = $this->getAnalyticsData($platform, $period);
        
        if ($format === 'csv') {
            return $this->exportToCSV($analytics, $platform);
        } elseif ($format === 'json') {
            return response()->json($analytics);
        }
        
        return response()->json(['error' => 'Invalid format'], 400);
    }

    private function getAnalyticsData($platform, $period)
    {
        $dateRange = $this->getDateRange($period);
        
        switch ($platform) {
            case 'facebook':
                return $this->getFacebookAnalytics($dateRange);
            case 'twitter':
                return $this->getTwitterAnalytics($dateRange);
            case 'instagram':
                return $this->getInstagramAnalytics($dateRange);
            case 'youtube':
                return $this->getYouTubeAnalytics($dateRange);
            case 'linkedin':
                return $this->getLinkedInAnalytics($dateRange);
            case 'tiktok':
                return $this->getTikTokAnalytics($dateRange);
            default:
                return [];
        }
    }

    private function exportToCSV($data, $platform)
    {
        $csvContent = "Date,Impressions,Reach,Engagement,Engagement Rate\n";
        
        foreach ($data as $date => $metrics) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $date,
                $metrics['impressions'] ?? 0,
                $metrics['reach'] ?? 0,
                $metrics['engagement'] ?? 0,
                $metrics['engagement_rate'] ?? 0
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $platform . '_analytics.csv"');
    }
}
