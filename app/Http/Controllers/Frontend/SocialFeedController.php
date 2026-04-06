<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SocialFeedController extends Controller
{
    public function index()
    {
        return view('frontend.social-feeds.index');
    }

    public function getFacebookFeed()
    {
        $cacheKey = 'facebook_feed_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() {
            $accessToken = env('FACEBOOK_ACCESS_TOKEN');
            $pageId = env('FACEBOOK_PAGE_ID');
            
            if (!$accessToken || !$pageId) {
                return [];
            }

            try {
                $response = Http::get("https://graph.facebook.com/v18.0/{$pageId}/posts", [
                    'access_token' => $accessToken,
                    'fields' => 'id,message,created_time,full_picture,permalink_url,likes.summary(true),comments.summary(true),shares',
                    'limit' => 10,
                ]);

                if ($response->successful()) {
                    return $this->formatFacebookFeed($response->json()['data'] ?? []);
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });

        return response()->json($feed);
    }

    public function getTwitterFeed()
    {
        $cacheKey = 'twitter_feed_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() {
            $bearerToken = env('TWITTER_BEARER_TOKEN');
            
            if (!$bearerToken) {
                return [];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                ])->get('https://api.twitter.com/2/users/me/tweets', [
                    'max_results' => 10,
                    'tweet.fields' => 'created_at,public_metrics,attachments,entities',
                    'expansions' => 'attachments.media,author_id',
                    'user.fields' => 'name,username,profile_image_url',
                ]);

                if ($response->successful()) {
                    return $this->formatTwitterFeed($response->json());
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });

        return response()->json($feed);
    }

    public function getInstagramFeed()
    {
        $cacheKey = 'instagram_feed_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() {
            $accessToken = env('INSTAGRAM_ACCESS_TOKEN');
            
            if (!$accessToken) {
                return [];
            }

            try {
                $response = Http::get("https://graph.instagram.com/v18.0/me/media", [
                    'access_token' => $accessToken,
                    'fields' => 'id,media_type,media_url,caption,permalink_url,timestamp,like_count,comments_count',
                    'limit' => 10,
                ]);

                if ($response->successful()) {
                    return $this->formatInstagramFeed($response->json()['data'] ?? []);
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });

        return response()->json($feed);
    }

    public function getLinkedInFeed()
    {
        $cacheKey = 'linkedin_feed_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() {
            $accessToken = env('LINKEDIN_ACCESS_TOKEN');
            $companyId = env('LINKEDIN_COMPANY_ID');
            
            if (!$accessToken || !$companyId) {
                return [];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get("https://api.linkedin.com/v2/shares", [
                    'q' => 'owners',
                    'owners' => 'urn:li:organization:' . $companyId,
                    'count' => 10,
                ]);

                if ($response->successful()) {
                    return $this->formatLinkedInFeed($response->json()['elements'] ?? []);
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });

        return response()->json($feed);
    }

    public function getYouTubeFeed()
    {
        $cacheKey = 'youtube_feed_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() {
            $apiKey = env('YOUTUBE_API_KEY');
            $channelId = env('YOUTUBE_CHANNEL_ID');
            
            if (!$apiKey || !$channelId) {
                return [];
            }

            try {
                $response = Http::get("https://www.googleapis.com/youtube/v3/search", [
                    'key' => $apiKey,
                    'channelId' => $channelId,
                    'part' => 'snippet',
                    'order' => 'date',
                    'maxResults' => 10,
                ]);

                if ($response->successful()) {
                    return $this->formatYouTubeFeed($response->json()['items'] ?? []);
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });

        return response()->json($feed);
    }

    public function getTikTokFeed()
    {
        $cacheKey = 'tiktok_feed_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() {
            $accessToken = env('TIKTOK_ACCESS_TOKEN');
            
            if (!$accessToken) {
                return [];
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])->get('https://open.tiktokapis.com/v2/video/list/', [
                    'fields' => 'id,title,description,duration,cover_image_url,embed_html,play_count,like_count,comment_count,share_count,create_time',
                    'count' => 10,
                ]);

                if ($response->successful()) {
                    return $this->formatTikTokFeed($response->json()['data']['videos'] ?? []);
                }
            } catch (\Exception $e) {
                return [];
            }

            return [];
        });

        return response()->json($feed);
    }

    public function getCombinedFeed()
    {
        $feeds = [
            'facebook' => $this->getFacebookFeed()->getData(true),
            'twitter' => $this->getTwitterFeed()->getData(true),
            'instagram' => $this->getInstagramFeed()->getData(true),
            'youtube' => $this->getYouTubeFeed()->getData(true),
        ];

        // Combine and sort by date
        $combined = [];
        foreach ($feeds as $platform => $feed) {
            foreach ($feed as $item) {
                $item['platform'] = $platform;
                $combined[] = $item;
            }
        }

        usort($combined, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json(array_slice($combined, 0, 20));
    }

    public function getFeedByHashtag($hashtag)
    {
        $cacheKey = 'hashtag_feed_' . $hashtag . '_' . date('Y-m-d-H');
        
        $feed = Cache::remember($cacheKey, 3600, function() use ($hashtag) {
            $results = [];

            // Instagram hashtag search
            $instagramResults = $this->searchInstagramHashtag($hashtag);
            if (!empty($instagramResults)) {
                $results = array_merge($results, $instagramResults);
            }

            // Twitter hashtag search
            $twitterResults = $this->searchTwitterHashtag($hashtag);
            if (!empty($twitterResults)) {
                $results = array_merge($results, $twitterResults);
            }

            // Sort by date
            usort($results, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return array_slice($results, 0, 20);
        });

        return response()->json($feed);
    }

    private function formatFacebookFeed($data)
    {
        $feed = [];
        
        foreach ($data as $post) {
            $feed[] = [
                'id' => $post['id'],
                'content' => $post['message'] ?? '',
                'image' => $post['full_picture'] ?? null,
                'url' => $post['permalink_url'] ?? '',
                'created_at' => $post['created_time'] ?? '',
                'likes' => $post['likes']['summary']['total_count'] ?? 0,
                'comments' => $post['comments']['summary']['total_count'] ?? 0,
                'shares' => $post['shares']['count'] ?? 0,
                'platform' => 'facebook',
                'type' => 'post',
            ];
        }

        return $feed;
    }

    private function formatTwitterFeed($data)
    {
        $feed = [];
        
        if (isset($data['data'])) {
            foreach ($data['data'] as $tweet) {
                $feed[] = [
                    'id' => $tweet['id'],
                    'content' => $tweet['text'] ?? '',
                    'image' => $this->getTwitterImage($tweet),
                    'url' => "https://twitter.com/i/web/status/{$tweet['id']}",
                    'created_at' => $tweet['created_at'] ?? '',
                    'likes' => $tweet['public_metrics']['like_count'] ?? 0,
                    'comments' => $tweet['public_metrics']['reply_count'] ?? 0,
                    'shares' => $tweet['public_metrics']['retweet_count'] ?? 0,
                    'platform' => 'twitter',
                    'type' => 'tweet',
                ];
            }
        }

        return $feed;
    }

    private function formatInstagramFeed($data)
    {
        $feed = [];
        
        foreach ($data as $post) {
            $feed[] = [
                'id' => $post['id'],
                'content' => $post['caption'] ?? '',
                'image' => $post['media_url'] ?? null,
                'url' => $post['permalink_url'] ?? '',
                'created_at' => $post['timestamp'] ?? '',
                'likes' => $post['like_count'] ?? 0,
                'comments' => $post['comments_count'] ?? 0,
                'shares' => 0, // Instagram doesn't have shares
                'platform' => 'instagram',
                'type' => $post['media_type'] ?? 'image',
            ];
        }

        return $feed;
    }

    private function formatLinkedInFeed($data)
    {
        $feed = [];
        
        foreach ($data as $post) {
            $feed[] = [
                'id' => $post['id'] ?? '',
                'content' => $post['text'] ?? '',
                'image' => $post['content']['entities'][0]['images'][0]['imageUrl'] ?? null,
                'url' => $post['url'] ?? '',
                'created_at' => $post['created']['time'] ?? '',
                'likes' => $post['totalShares'] ?? 0,
                'comments' => $post['totalComments'] ?? 0,
                'shares' => $post['totalShares'] ?? 0,
                'platform' => 'linkedin',
                'type' => 'post',
            ];
        }

        return $feed;
    }

    private function formatYouTubeFeed($data)
    {
        $feed = [];
        
        foreach ($data as $video) {
            $feed[] = [
                'id' => $video['id']['videoId'],
                'content' => $video['snippet']['description'] ?? '',
                'image' => $video['snippet']['thumbnails']['high']['url'] ?? null,
                'url' => 'https://www.youtube.com/watch?v=' . $video['id']['videoId'],
                'created_at' => $video['snippet']['publishedAt'] ?? '',
                'likes' => 0, // YouTube API doesn't provide likes in basic search
                'comments' => 0, // YouTube API doesn't provide comments in basic search
                'shares' => 0, // YouTube doesn't have shares
                'platform' => 'youtube',
                'type' => 'video',
                'title' => $video['snippet']['title'] ?? '',
            ];
        }

        return $feed;
    }

    private function formatTikTokFeed($data)
    {
        $feed = [];
        
        foreach ($data as $video) {
            $feed[] = [
                'id' => $video['id'],
                'content' => $video['description'] ?? '',
                'image' => $video['cover_image_url'] ?? null,
                'url' => $video['embed_html'] ?? '',
                'created_at' => date('Y-m-d H:i:s', $video['create_time'] ?? time()),
                'likes' => $video['like_count'] ?? 0,
                'comments' => $video['comment_count'] ?? 0,
                'shares' => $video['share_count'] ?? 0,
                'platform' => 'tiktok',
                'type' => 'video',
                'duration' => $video['duration'] ?? 0,
            ];
        }

        return $feed;
    }

    private function getTwitterImage($tweet)
    {
        if (isset($tweet['attachments']['media'][0]['media_key'])) {
            $mediaKey = $tweet['attachments']['media'][0]['media_key'];
            
            // Get media details
            if (isset($tweet['includes']['media'])) {
                foreach ($tweet['includes']['media'] as $media) {
                    if ($media['media_key'] === $mediaKey && $media['type'] === 'photo') {
                        return $media['url'];
                    }
                }
            }
        }
        
        return null;
    }

    private function searchInstagramHashtag($hashtag)
    {
        $accessToken = env('INSTAGRAM_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::get("https://graph.instagram.com/v18.0/ig_hashtag_search", [
                'user_id' => env('INSTAGRAM_BUSINESS_ACCOUNT_ID'),
                'q' => $hashtag,
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $hashtagId = $response->json()['data'][0]['id'] ?? null;
                
                if ($hashtagId) {
                    $mediaResponse = Http::get("https://graph.instagram.com/v18.0/{$hashtagId}/recent_media", [
                        'user_id' => env('INSTAGRAM_BUSINESS_ACCOUNT_ID'),
                        'fields' => 'id,media_type,media_url,caption,permalink_url,timestamp,like_count,comments_count',
                        'limit' => 10,
                        'access_token' => $accessToken,
                    ]);

                    if ($mediaResponse->successful()) {
                        return $this->formatInstagramFeed($mediaResponse->json()['data'] ?? []);
                    }
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    private function searchTwitterHashtag($hashtag)
    {
        $bearerToken = env('TWITTER_BEARER_TOKEN');
        
        if (!$bearerToken) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken,
            ])->get('https://api.twitter.com/2/tweets/search/recent', [
                'query' => '#' . $hashtag,
                'max_results' => 10,
                'tweet.fields' => 'created_at,public_metrics,attachments,entities',
                'expansions' => 'attachments.media,author_id',
                'user.fields' => 'name,username,profile_image_url',
            ]);

            if ($response->successful()) {
                return $this->formatTwitterFeed($response->json());
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getFeedSettings()
    {
        $settings = [
            'facebook' => [
                'enabled' => env('FACEBOOK_ACCESS_TOKEN') && env('FACEBOOK_PAGE_ID'),
                'page_id' => env('FACEBOOK_PAGE_ID'),
                'auto_refresh' => true,
                'refresh_interval' => 3600, // 1 hour
            ],
            'twitter' => [
                'enabled' => env('TWITTER_BEARER_TOKEN'),
                'auto_refresh' => true,
                'refresh_interval' => 1800, // 30 minutes
            ],
            'instagram' => [
                'enabled' => env('INSTAGRAM_ACCESS_TOKEN'),
                'auto_refresh' => true,
                'refresh_interval' => 3600, // 1 hour
            ],
            'youtube' => [
                'enabled' => env('YOUTUBE_API_KEY') && env('YOUTUBE_CHANNEL_ID'),
                'channel_id' => env('YOUTUBE_CHANNEL_ID'),
                'auto_refresh' => true,
                'refresh_interval' => 3600, // 1 hour
            ],
            'linkedin' => [
                'enabled' => env('LINKEDIN_ACCESS_TOKEN') && env('LINKEDIN_COMPANY_ID'),
                'company_id' => env('LINKEDIN_COMPANY_ID'),
                'auto_refresh' => true,
                'refresh_interval' => 3600, // 1 hour
            ],
            'tiktok' => [
                'enabled' => env('TIKTOK_ACCESS_TOKEN'),
                'auto_refresh' => true,
                'refresh_interval' => 3600, // 1 hour
            ],
        ];

        return response()->json($settings);
    }

    public function updateFeedSettings(Request $request)
    {
        $settings = $request->validate([
            'facebook.enabled' => 'boolean',
            'facebook.auto_refresh' => 'boolean',
            'facebook.refresh_interval' => 'integer|min:300',
            'twitter.enabled' => 'boolean',
            'twitter.auto_refresh' => 'boolean',
            'twitter.refresh_interval' => 'integer|min:300',
            'instagram.enabled' => 'boolean',
            'instagram.auto_refresh' => 'boolean',
            'instagram.refresh_interval' => 'integer|min:300',
            'youtube.enabled' => 'boolean',
            'youtube.auto_refresh' => 'boolean',
            'youtube.refresh_interval' => 'integer|min:300',
            'linkedin.enabled' => 'boolean',
            'linkedin.auto_refresh' => 'boolean',
            'linkedin.refresh_interval' => 'integer|min:300',
            'tiktok.enabled' => 'boolean',
            'tiktok.auto_refresh' => 'boolean',
            'tiktok.refresh_interval' => 'integer|min:300',
        ]);

        // Store settings in cache or database
        Cache::put('social_feed_settings', $settings, 86400);

        return response()->json([
            'success' => true,
            'message' => 'Feed settings updated successfully',
            'settings' => $settings
        ]);
    }

    public function clearFeedCache()
    {
        $platforms = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin', 'tiktok'];
        
        foreach ($platforms as $platform) {
            Cache::forget($platform . '_feed_' . date('Y-m-d-H'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Feed cache cleared successfully'
        ]);
    }
}
