<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        $config = $this->getProviderConfig($provider);
        
        return Socialite::driver($provider)
            ->scopes($config['scopes'])
            ->redirect();
    }

    public function callback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            
            // Check if user already exists
            $existingUser = User::where('email', $user->getEmail())->first();
            
            if ($existingUser) {
                // Login existing user
                Auth::login($existingUser);
                return redirect()->route('dashboard');
            } else {
                // Create new user
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => bcrypt(Str::random(16)), // Random password
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'avatar' => $user->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $user->getId(),
                    'provider_token' => $user->token,
                    'provider_refresh_token' => $user->refreshToken,
                ]);
                
                Auth::login($newUser);
                return redirect()->route('dashboard')->with('success', 'Account created successfully!');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Social login failed. Please try again.');
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            
            $authUser = User::where('provider', $provider)
                ->where('provider_id', $user->getId())
                ->first();

            if ($authUser) {
                // Update user info
                $authUser->update([
                    'avatar' => $user->getAvatar(),
                    'provider_token' => $user->token,
                    'provider_refresh_token' => $user->refreshToken,
                ]);
                
                Auth::login($authUser);
                return redirect()->route('dashboard');
            } else {
                // Create new user
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'avatar' => $user->getAvatar(),
                    'provider' => $provider,
                    'provider_id' => $user->getId(),
                    'provider_token' => $user->token,
                    'provider_refresh_token' => $user->refreshToken,
                ]);
                
                Auth::login($newUser);
                return redirect()->route('dashboard')->with('success', 'Account created successfully!');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Social login failed. Please try again.');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    public function connect($provider)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $config = $this->getProviderConfig($provider);
        
        return Socialite::driver($provider)
            ->scopes($config['scopes'])
            ->redirect();
    }

    public function connectCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            $authUser = Auth::user();
            
            // Link social account to existing user
            $authUser->update([
                'provider' => $provider,
                'provider_id' => $user->getId(),
                'provider_token' => $user->token,
                'provider_refresh_token' => $user->refreshToken,
                'avatar' => $user->getAvatar(),
            ]);
            
            return redirect()->route('account.settings')->with('success', 'Social account linked successfully!');
        } catch (\Exception $e) {
            return redirect()->route('account.settings')->with('error', 'Failed to link social account.');
        }
    }

    public function disconnect($provider)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $authUser = Auth::user();
        
        // Remove social account link
        if ($authUser->provider === $provider) {
            $authUser->update([
                'provider' => null,
                'provider_id' => null,
                'provider_token' => null,
                'provider_refresh_token' => null,
            ]);
        }
        
        return redirect()->route('account.settings')->with('success', 'Social account disconnected successfully!');
    }

    private function getProviderConfig($provider)
    {
        $configs = [
            'google' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect' => env('GOOGLE_REDIRECT_URI'),
                'scopes' => ['openid', 'profile', 'email'],
            ],
            'facebook' => [
                'client_id' => env('FACEBOOK_CLIENT_ID'),
                'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
                'redirect' => env('FACEBOOK_REDIRECT_URI'),
                'scopes' => ['email', 'public_profile'],
            ],
            'twitter' => [
                'client_id' => env('TWITTER_CLIENT_ID'),
                'client_secret' => env('TWITTER_CLIENT_SECRET'),
                'redirect' => env('TWITTER_REDIRECT_URI'),
                'scopes' => ['tweet.read', 'users.read', 'email'],
            ],
            'linkedin' => [
                'client_id' => env('LINKEDIN_CLIENT_ID'),
                'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
                'redirect' => env('LINKEDIN_REDIRECT_URI'),
                'scopes' => ['r_liteprofile', 'r_emailaddress'],
            ],
            'instagram' => [
                'client_id' => env('INSTAGRAM_CLIENT_ID'),
                'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
                'redirect' => env('INSTAGRAM_REDIRECT_URI'),
                'scopes' => ['basic', 'public_profile', 'media'],
            ],
            'github' => [
                'client_id' => env('GITHUB_CLIENT_ID'),
                'client_secret' => env('GITHUB_CLIENT_SECRET'),
                'redirect' => env('GITHUB_REDIRECT_URI'),
                'scopes' => ['user:email'],
            ],
        ];

        return $configs[$provider] ?? [];
    }

    public function getSocialLoginButtons()
    {
        $buttons = [];
        
        if (env('GOOGLE_CLIENT_ID')) {
            $buttons[] = [
                'provider' => 'google',
                'name' => 'Google',
                'icon' => 'fab fa-google',
                'color' => '#4285F4',
                'url' => route('social.auth.redirect', 'google'),
            ];
        }

        if (env('FACEBOOK_CLIENT_ID')) {
            $buttons[] = [
                'provider' => 'facebook',
                'name' => 'Facebook',
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877F2',
                'url' => route('social.auth.redirect', 'facebook'),
            ];
        }

        if (env('TWITTER_CLIENT_ID')) {
            $buttons[] = [
                'provider' => 'twitter',
                'name' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'color' => '#1DA1F2',
                'url' => route('social.auth.redirect', 'twitter'),
            ];
        }

        if (env('LINKEDIN_CLIENT_ID')) {
            $buttons[] = [
                'provider' => 'linkedin',
                'name' => 'LinkedIn',
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0077B5',
                'url' => route('social.auth.redirect', 'linkedin'),
            ];
        }

        if (env('INSTAGRAM_CLIENT_ID')) {
            $buttons[] = [
                'provider' => 'instagram',
                'name' => 'Instagram',
                'icon' => 'fab fa-instagram',
                'color' => '#E4405F',
                'url' => route('social.auth.redirect', 'instagram'),
            ];
        }

        if (env('GITHUB_CLIENT_ID')) {
            $buttons[] = [
                'provider' => 'github',
                'name' => 'GitHub',
                'icon' => 'fab fa-github',
                'color' => '#333333',
                'url' => route('social.auth.redirect', 'github'),
            ];
        }

        return $buttons;
    }

    public function getConnectedAccounts()
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $connectedAccounts = [];

        if ($user->provider && $user->provider_id) {
            $connectedAccounts[] = [
                'provider' => $user->provider,
                'provider_id' => $user->provider_id,
                'avatar' => $user->avatar,
                'connected_at' => $user->updated_at,
            ];
        }

        return $connectedAccounts;
    }

    public function getSocialProfileData($provider)
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();
        
        if ($user->provider === $provider && $user->provider_id) {
            try {
                $socialUser = Socialite::driver($provider)->userFromToken($user->provider_token);
                
                return [
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'profile_url' => $this->getProfileUrl($provider, $socialUser),
                    'followers' => $this->getFollowersCount($provider, $socialUser),
                    'verified' => $this->isVerified($provider, $socialUser),
                ];
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    private function getProfileUrl($provider, $user)
    {
        switch ($provider) {
            case 'google':
                return $user->user['url'] ?? null;
            case 'facebook':
                return $user->user['link'] ?? null;
            case 'twitter':
                return 'https://twitter.com/' . $user->getNickname();
            case 'linkedin':
                return $user->user['url'] ?? null;
            case 'instagram':
                return 'https://instagram.com/' . $user->user['username'] ?? null;
            case 'github':
                return $user->user['html_url'] ?? null;
            default:
                return null;
        }
    }

    private function getFollowersCount($provider, $user)
    {
        switch ($provider) {
            case 'twitter':
                return $user->user['followers_count'] ?? 0;
            case 'instagram':
                return $user->user['media_count'] ?? 0;
            case 'facebook':
                return null; // Facebook doesn't provide followers count
            case 'linkedin':
                $connections = $user->user['connections'] ?? [];
                return count($connections);
            case 'github':
                return $user->user['public_repos'] ?? 0;
            default:
                return 0;
        }
    }

    private function isVerified($provider, $user)
    {
        switch ($provider) {
            case 'twitter':
                return $user->user['verified'] ?? false;
            case 'instagram':
                return $user->user['verified'] ?? false;
            case 'facebook':
                return $user->user['verified'] ?? false;
            case 'linkedin':
                return $user->user['verified'] ?? false;
            case 'github':
                $email = $user->getEmail();
                return $email && $user->user['email_verified'] ?? false;
            default:
                false;
        }
    }

    public function updateSocialProfile($provider)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $user = Auth::user();
            $socialUser = Socialite::driver($provider)->userFromToken($user->provider_token);
            
            $user->update([
                'name' => $socialUser->getName(),
                'avatar' => $socialUser->getAvatar(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update profile',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function unlinkSocialAccount($provider)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        
        if ($user->provider === $provider) {
            $user->update([
                'provider' => null,
                'provider_id' => null,
                'provider_token' => null,
                'provider_refresh_token' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Social account unlinked successfully'
            ]);
        }

        return response()->json([
            'error' => 'No social account found for this provider',
            'message' => 'No social account found for ' . $provider
        ], 404);
    }

    public function getSocialShareData($provider, $url)
    {
        $shareData = [];
        
        switch ($provider) {
            case 'facebook':
                $shareData = [
                    'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
                    'title' => 'Check out this amazing product!',
                    'description' => 'I found this great product and thought you might like it.',
                    'image' => asset('images/og-image.jpg'),
                ];
                break;
                
            case 'twitter':
                $shareData = [
                    'url' => 'https://twitter.com/intent/tweet?url=' . urlencode($url),
                    'title' => 'Check out this amazing product!',
                    'text' => 'I found this great product and thought you might like it.',
                ];
                break;
                
            case 'linkedin':
                $shareData = [
                    'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url),
                    'title' => 'Check out this amazing product!',
                    'summary' => 'I found this great product and thought you might like it.',
                ];
                break;
                
            case 'pinterest':
                $shareData = [
                    'url' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($url),
                    'description' => 'I found this great product and thought you might like it.',
                    'media' => asset('images/og-image.jpg'),
                ];
                break;
                
            case 'whatsapp':
                $shareData = [
                    'url' => 'https://wa.me/?text=' . urlencode('Check out this amazing product! ' . $url),
                ];
                break;
                
            case 'email':
                $shareData = [
                    'url' => $url,
                    'subject' => 'Check out this amazing product!',
                    'body' => 'I found this great product and thought you might like it. Check it out: ' . $url,
                ];
                break;
        }

        return $shareData;
    }
}
