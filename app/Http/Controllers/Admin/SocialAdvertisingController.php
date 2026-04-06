<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\SocialAdCampaignCreateRequest;
use App\Http\Requests\Admin\SocialAdCampaignUpdateRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SocialAdvertisingController extends Controller
{
    public function index()
    {
        return view('admin.social-advertising.index');
    }

    public function getAdAccounts()
    {
        $accounts = [
            'facebook' => $this->getFacebookAdAccounts(),
            'google' => $this->getGoogleAdAccounts(),
            'twitter' => $this->getTwitterAdAccounts(),
            'linkedin' => $this->getLinkedInAdAccounts(),
            'tiktok' => $this->getTikTokAdAccounts(),
        ];

        return response()->json($accounts);
    }

    public function getFacebookAdAccounts()
    {
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::get("https://graph.facebook.com/v18.0/me/adaccounts", [
                'access_token' => $accessToken,
                'fields' => 'account_id,name,account_status,balance,currency,amount_spent',
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getGoogleAdAccounts()
    {
        $accessToken = env('GOOGLE_ADS_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://googleads.googleapis.com/v16/customers:listAccessibleCustomers");

            if ($response->successful()) {
                return $response->json()['resourceNames'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getTwitterAdAccounts()
    {
        $accessToken = env('TWITTER_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://ads-api.twitter.com/11/accounts');

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getLinkedInAdAccounts()
    {
        $accessToken = env('LINKEDIN_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://api.linkedin.com/v2/adAccounts');

            if ($response->successful()) {
                return $response->json()['elements'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getTikTokAdAccounts()
    {
        $accessToken = env('TIKTOK_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://business-api.tiktok.com/open_api/v1.3/advertiser/get/');

            if ($response->successful()) {
                return $response->json()['data']['list'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function createCampaign(SocialAdCampaignCreateRequest $request)
    {
        $campaign = [
            'name' => $request->name,
            'platform' => $request->platform,
            'objective' => $request->objective,
            'budget' => $request->budget,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'target_audience' => $request->target_audience,
            'creative_assets' => $request->creative_assets,
            'bid_strategy' => $request->bid_strategy,
            'status' => $request->status,
            'created_at' => now(),
        ];

        // Create campaign on the platform
        $platformCampaign = $this->createPlatformCampaign($campaign);

        return response()->json([
            'success' => true,
            'message' => 'Campaign created successfully',
            'campaign' => array_merge($campaign, $platformCampaign),
        ]);
    }

    public function createPlatformCampaign($campaign)
    {
        switch ($campaign['platform']) {
            case 'facebook':
                return $this->createFacebookCampaign($campaign);
            case 'google':
                return $this->createGoogleCampaign($campaign);
            case 'twitter':
                return $this->createTwitterCampaign($campaign);
            case 'linkedin':
                return $this->createLinkedInCampaign($campaign);
            case 'tiktok':
                return $this->createTikTokCampaign($campaign);
            default:
                return [];
        }
    }

    public function createFacebookCampaign($campaign)
    {
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        $accountId = env('FACEBOOK_AD_ACCOUNT_ID');
        
        if (!$accessToken || !$accountId) {
            return [];
        }

        try {
            $response = Http::post("https://graph.facebook.com/v18.0/act_{$accountId}/campaigns", [
                'access_token' => $accessToken,
                'name' => $campaign['name'],
                'objective' => $campaign['objective'],
                'status' => $campaign['status'],
                'special_ad_categories' => [],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function createGoogleCampaign($campaign)
    {
        $accessToken = env('GOOGLE_ADS_ACCESS_TOKEN');
        $customerId = env('GOOGLE_ADS_CUSTOMER_ID');
        
        if (!$accessToken || !$customerId) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'developer-token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
            ])->post("https://googleads.googleapis.com/v16/customers/{$customerId}:mutate", [
                'mutateOperations' => [
                    [
                        'campaignCreateOperation' => [
                            'campaign' => [
                                'name' => $campaign['name'],
                                'advertisingChannelType' => 'SEARCH',
                                'status' => strtoupper($campaign['status']),
                                'campaignBudget' => [
                                    'amountMicros' => $campaign['budget'] * 1000000,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function createTwitterCampaign($campaign)
    {
        $accessToken = env('TWITTER_ACCESS_TOKEN');
        $accountId = env('TWITTER_AD_ACCOUNT_ID');
        
        if (!$accessToken || !$accountId) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post("https://ads-api.twitter.com/11/accounts/{$accountId}/campaigns", [
                'name' => $campaign['name'],
                'entity_status' => $campaign['status'],
                'objective' => $campaign['objective'],
                'total_budget_amount_local_micro' => $campaign['budget'] * 1000000,
                'currency' => 'USD',
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function createLinkedInCampaign($campaign)
    {
        $accessToken = env('LINKEDIN_ACCESS_TOKEN');
        $accountId = env('LINKEDIN_AD_ACCOUNT_ID');
        
        if (!$accessToken || !$accountId) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.linkedin.com/v2/adCampaigns', [
                'account' => $accountId,
                'name' => $campaign['name'],
                'status' => $campaign['status'],
                'type' => $campaign['objective'],
                'budget' => [
                    'amount' => $campaign['budget'],
                    'currency' => 'USD',
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function createTikTokCampaign($campaign)
    {
        $accessToken = env('TIKTOK_ACCESS_TOKEN');
        $advertiserId = env('TIKTOK_ADVERTISER_ID');
        
        if (!$accessToken || !$advertiserId) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://business-api.tiktok.com/open_api/v1.3/campaign/create/', [
                'advertiser_id' => $advertiserId,
                'campaign_name' => $campaign['name'],
                'objective_type' => $this->mapTikTokObjective($campaign['objective']),
                'budget_mode' => 'BUDGET_MODE_DAY',
                'budget' => $campaign['budget'],
                'status' => $campaign['status'],
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            return [];
        }

        return [];
    }

    public function getCampaigns()
    {
        $platform = request()->get('platform');
        $period = request()->get('period', '30days');
        
        switch ($platform) {
            case 'facebook':
                return $this->getFacebookCampaigns();
            case 'google':
                return $this->getGoogleCampaigns();
            case 'twitter':
                return $this->getTwitterCampaigns();
            case 'linkedin':
                return $this->getLinkedInCampaigns();
            case 'tiktok':
                return $this->getTikTokCampaigns();
            default:
                return response()->json([]);
        }
    }

    public function getFacebookCampaigns()
    {
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        $accountId = env('FACEBOOK_AD_ACCOUNT_ID');
        
        if (!$accessToken || !$accountId) {
            return response()->json([]);
        }

        try {
            $response = Http::get("https://graph.facebook.com/v18.0/act_{$accountId}/campaigns", [
                'access_token' => $accessToken,
                'fields' => 'id,name,status,objective,buying_type,start_time,stop_time,spend,budget_remaining',
                'limit' => 50,
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getGoogleCampaigns()
    {
        $accessToken = env('GOOGLE_ADS_ACCESS_TOKEN');
        $customerId = env('GOOGLE_ADS_CUSTOMER_ID');
        
        if (!$accessToken || !$customerId) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'developer-token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
            ])->post("https://googleads.googleapis.com/v16/customers/{$customerId}:search", [
                'query' => 'SELECT campaign.id, campaign.name, campaign.status, campaign.advertising_channel_type FROM campaign',
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['results'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getTwitterCampaigns()
    {
        $accessToken = env('TWITTER_ACCESS_TOKEN');
        $accountId = env('TWITTER_AD_ACCOUNT_ID');
        
        if (!$accessToken || !$accountId) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://ads-api.twitter.com/11/accounts/{$accountId}/campaigns", [
                'with_total_count' => true,
                'count' => 50,
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getLinkedInCampaigns()
    {
        $accessToken = env('LINKEDIN_ACCESS_TOKEN');
        $accountId = env('LINKEDIN_AD_ACCOUNT_ID');
        
        if (!$accessToken || !$accountId) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://api.linkedin.com/v2/adCampaigns', [
                'q' => 'account',
                'account' => $accountId,
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['elements'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getTikTokCampaigns()
    {
        $accessToken = env('TIKTOK_ACCESS_TOKEN');
        $advertiserId = env('TIKTOK_ADVERTISER_ID');
        
        if (!$accessToken || !$advertiserId) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://business-api.tiktok.com/open_api/v1.3/campaign/get/', [
                'advertiser_id' => $advertiserId,
                'fields' => 'campaign_id,campaign_name,status,budget,objective_type',
                'page_size' => 50,
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data']['list'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getCampaignMetrics($campaignId, $platform)
    {
        $period = request()->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        switch ($platform) {
            case 'facebook':
                return $this->getFacebookCampaignMetrics($campaignId, $dateRange);
            case 'google':
                return $this->getGoogleCampaignMetrics($campaignId, $dateRange);
            case 'twitter':
                return $this->getTwitterCampaignMetrics($campaignId, $dateRange);
            case 'linkedin':
                return $this->getLinkedInCampaignMetrics($campaignId, $dateRange);
            case 'tiktok':
                return $this->getTikTokCampaignMetrics($campaignId, $dateRange);
            default:
                return response()->json([]);
        }
    }

    public function getFacebookCampaignMetrics($campaignId, $dateRange)
    {
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json([]);
        }

        try {
            $response = Http::get("https://graph.facebook.com/v18.0/{$campaignId}/insights", [
                'access_token' => $accessToken,
                'fields' => 'impressions,clicks,spend,cpc,ctr,actions',
                'time_range' => $this->getFacebookTimeRange($dateRange),
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getGoogleCampaignMetrics($campaignId, $dateRange)
    {
        $accessToken = env('GOOGLE_ADS_ACCESS_TOKEN');
        $customerId = env('GOOGLE_ADS_CUSTOMER_ID');
        
        if (!$accessToken || !$customerId) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'developer-token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
            ])->post("https://googleads.googleapis.com/v16/customers/{$customerId}:search", [
                'query' => 'SELECT metrics.impressions, metrics.clicks, metrics.cost, metrics.ctr, metrics.cpc FROM campaign WHERE campaign.id = ' . $campaignId,
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['results'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getTwitterCampaignMetrics($campaignId, $dateRange)
    {
        $accessToken = env('TWITTER_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://ads-api.twitter.com/11/stats/campaign/{$campaignId}", [
                'entity' => 'CAMPAIGN',
                'entity_ids' => $campaignId,
                'start_time' => $dateRange['start']->format('Y-m-d'),
                'end_time' => $dateRange['end']->format('Y-m-d'),
                'granularity' => 'DAY',
                'metric_groups' => 'BILLING,ENGAGEMENT',
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getLinkedInCampaignMetrics($campaignId, $dateRange)
    {
        $accessToken = env('LINKEDIN_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://api.linkedin.com/v2/adAnalytics', [
                'q' => 'analytics',
                'campaign' => $campaignId,
                'timeRange' => $this->getLinkedInTimeRange($dateRange),
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['elements'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function getTikTokCampaignMetrics($campaignId, $dateRange)
    {
        $accessToken = env('TIKTOK_ACCESS_TOKEN');
        $advertiserId = env('TIKTOK_ADVERTISER_ID');
        
        if (!$accessToken || !$advertiserId) {
            return response()->json([]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get('https://business-api.tiktok.com/open_api/v1.3/campaign/get/', [
                'advertiser_id' => $advertiserId,
                'campaign_ids' => $campaignId,
                'fields' => 'campaign_id,campaign_name,status,budget,objective_type,impressions,clicks,spend',
            ]);

            if ($response->successful()) {
                return response()->json($response->json()['data']['list'] ?? []);
            }
        } catch (\Exception $e) {
            return response()->json([]);
        }

        return response()->json([]);
    }

    public function updateCampaign(SocialAdCampaignUpdateRequest $request, $campaignId, $platform)
    {
        $updateData = $request->only(['name', 'budget', 'status', 'bid_strategy']);
        
        switch ($platform) {
            case 'facebook':
                return $this->updateFacebookCampaign($campaignId, $updateData);
            case 'google':
                return $this->updateGoogleCampaign($campaignId, $updateData);
            case 'twitter':
                return $this->updateTwitterCampaign($campaignId, $updateData);
            case 'linkedin':
                return $this->updateLinkedInCampaign($campaignId, $updateData);
            case 'tiktok':
                return $this->updateTikTokCampaign($campaignId, $updateData);
            default:
                return response()->json(['error' => 'Invalid platform'], 400);
        }
    }

    public function updateFacebookCampaign($campaignId, $data)
    {
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 400);
        }

        try {
            $response = Http::post("https://graph.facebook.com/v18.0/{$campaignId}", [
                'access_token' => $accessToken,
                ...$data,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign updated successfully',
                    'campaign' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update campaign'], 500);
        }

        return response()->json(['error' => 'Failed to update campaign'], 500);
    }

    public function updateGoogleCampaign($campaignId, $data)
    {
        $accessToken = env('GOOGLE_ADS_ACCESS_TOKEN');
        $customerId = env('GOOGLE_ADS_CUSTOMER_ID');
        
        if (!$accessToken || !$customerId) {
            return response()->json(['error' => 'Missing credentials'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'developer-token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
            ])->post("https://googleads.googleapis.com/v16/customers/{$customerId}:mutate", [
                'mutateOperations' => [
                    [
                        'campaignUpdateOperation' => [
                            'campaign' => [
                                'resourceName' => "customers/{$customerId}/campaigns/{$campaignId}",
                                ...$data,
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign updated successfully',
                    'campaign' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update campaign'], 500);
        }

        return response()->json(['error' => 'Failed to update campaign'], 500);
    }

    public function updateTwitterCampaign($campaignId, $data)
    {
        $accessToken = env('TWITTER_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->put("https://ads-api.twitter.com/11/campaigns/{$campaignId}", $data);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign updated successfully',
                    'campaign' => $response->json()['data'] ?? [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update campaign'], 500);
        }

        return response()->json(['error' => 'Failed to update campaign'], 500);
    }

    public function updateLinkedInCampaign($campaignId, $data)
    {
        $accessToken = env('LINKEDIN_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post("https://api.linkedin.com/v2/adCampaigns/{$campaignId}", $data);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign updated successfully',
                    'campaign' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update campaign'], 500);
        }

        return response()->json(['error' => 'Failed to update campaign'], 500);
    }

    public function updateTikTokCampaign($campaignId, $data)
    {
        $accessToken = env('TIKTOK_ACCESS_TOKEN');
        $advertiserId = env('TIKTOK_ADVERTISER_ID');
        
        if (!$accessToken || !$advertiserId) {
            return response()->json(['error' => 'Missing credentials'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://business-api.tiktok.com/open_api/v1.3/campaign/update/', [
                'advertiser_id' => $advertiserId,
                'campaign_id' => $campaignId,
                ...$data,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign updated successfully',
                    'campaign' => $response->json()['data'] ?? [],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update campaign'], 500);
        }

        return response()->json(['error' => 'Failed to update campaign'], 500);
    }

    public function deleteCampaign($campaignId, $platform)
    {
        switch ($platform) {
            case 'facebook':
                return $this->deleteFacebookCampaign($campaignId);
            case 'google':
                return $this->deleteGoogleCampaign($campaignId);
            case 'twitter':
                return $this->deleteTwitterCampaign($campaignId);
            case 'linkedin':
                return $this->deleteLinkedInCampaign($campaignId);
            case 'tiktok':
                return $this->deleteTikTokCampaign($campaignId);
            default:
                return response()->json(['error' => 'Invalid platform'], 400);
        }
    }

    public function deleteFacebookCampaign($campaignId)
    {
        $accessToken = env('FACEBOOK_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 400);
        }

        try {
            $response = Http::delete("https://graph.facebook.com/v18.0/{$campaignId}", [
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign deleted successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete campaign'], 500);
        }

        return response()->json(['error' => 'Failed to delete campaign'], 500);
    }

    public function deleteGoogleCampaign($campaignId)
    {
        $accessToken = env('GOOGLE_ADS_ACCESS_TOKEN');
        $customerId = env('GOOGLE_ADS_CUSTOMER_ID');
        
        if (!$accessToken || !$customerId) {
            return response()->json(['error' => 'Missing credentials'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'developer-token' => env('GOOGLE_ADS_DEVELOPER_TOKEN'),
            ])->post("https://googleads.googleapis.com/v16/customers/{$customerId}:mutate", [
                'mutateOperations' => [
                    [
                        'campaignRemoveOperation' => [
                            'resourceName' => "customers/{$customerId}/campaigns/{$campaignId}",
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign deleted successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete campaign'], 500);
        }

        return response()->json(['error' => 'Failed to delete campaign'], 500);
    }

    public function deleteTwitterCampaign($campaignId)
    {
        $accessToken = env('TWITTER_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->delete("https://ads-api.twitter.com/11/campaigns/{$campaignId}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign deleted successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete campaign'], 500);
        }

        return response()->json(['error' => 'Failed to delete campaign'], 500);
    }

    public function deleteLinkedInCampaign($campaignId)
    {
        $accessToken = env('LINKEDIN_ACCESS_TOKEN');
        
        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->delete("https://api.linkedin.com/v2/adCampaigns/{$campaignId}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign deleted successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete campaign'], 500);
        }

        return response()->json(['error' => 'Failed to delete campaign'], 500);
    }

    public function deleteTikTokCampaign($campaignId)
    {
        $accessToken = env('TIKTOK_ACCESS_TOKEN');
        $advertiserId = env('TIKTOK_ADVERTISER_ID');
        
        if (!$accessToken || !$advertiserId) {
            return response()->json(['error' => 'Missing credentials'], 400);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://business-api.tiktok.com/open_api/v1.3/campaign/delete/', [
                'advertiser_id' => $advertiserId,
                'campaign_ids' => $campaignId,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign deleted successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete campaign'], 500);
        }

        return response()->json(['error' => 'Failed to delete campaign'], 500);
    }

    private function mapTikTokObjective($objective)
    {
        $mapping = [
            'awareness' => 'AWARENESS',
            'consideration' => 'CONSIDERATION',
            'conversion' => 'CONVERSION',
            'reach' => 'REACH',
            'traffic' => 'TRAFFIC',
            'engagement' => 'ENGAGEMENT',
            'app_install' => 'APP_INSTALL',
            'video_views' => 'VIDEO_VIEWS',
        ];

        return $mapping[$objective] ?? 'CONVERSION';
    }

    private function getFacebookTimeRange($dateRange)
    {
        return $dateRange['start']->format('Y-m-d') . '_' . $dateRange['end']->format('Y-m-d');
    }

    private function getLinkedInTimeRange($dateRange)
    {
        return ($dateRange['end']->timestamp - $dateRange['start']->timestamp) / 86400; // days
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
}
