<?php

namespace App\Services;

use App\Models\SuspiciousCustomer;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuspiciousCustomerDetectionService
{
    public function analyzeCustomer($email, $phone = null, $ipAddress = null, $name = null)
    {
        $riskScore = 0;
        $riskFactors = [];

        // Check if already in suspicious list
        $suspiciousCustomer = SuspiciousCustomer::where('email', $email)->first();
        
        if (!$suspiciousCustomer) {
            // Analyze various risk factors
            $riskScore += $this->checkEmailDomain($email, $riskFactors);
            $riskScore += $this->checkPhonePattern($phone, $riskFactors);
            $riskScore += $this->checkIPAddress($ipAddress, $riskFactors);
            $riskScore += $this->checkExistingUserBehavior($email, $riskFactors);
            $riskScore += $this->checkOrderHistory($email, $riskFactors);
            $riskScore += $this->checkNamePattern($name, $riskFactors);

            // If risk score is above threshold, create suspicious customer record
            if ($riskScore >= 30) {
                $suspiciousCustomer = SuspiciousCustomer::create([
                    'email' => $email,
                    'phone' => $phone,
                    'name' => $name,
                    'ip_address' => $ipAddress,
                    'risk_score' => min(100, $riskScore),
                    'risk_factors' => $riskFactors,
                    'detection_method' => 'auto',
                    'reason' => 'Automatically flagged due to suspicious behavior patterns',
                ]);

                Log::info('Suspicious customer detected', [
                    'email' => $email,
                    'risk_score' => $riskScore,
                    'risk_factors' => $riskFactors
                ]);
            }
        } else {
            // Update existing record with new information
            $suspiciousCustomer->update([
                'phone' => $phone ?: $suspiciousCustomer->phone,
                'name' => $name ?: $suspiciousCustomer->name,
                'ip_address' => $ipAddress ?: $suspiciousCustomer->ip_address,
            ]);
        }

        return $suspiciousCustomer;
    }

    public function flagFakeOrder($email, $orderDetails = [])
    {
        $suspiciousCustomer = SuspiciousCustomer::where('email', $email)->first();
        
        if (!$suspiciousCustomer) {
            $suspiciousCustomer = SuspiciousCustomer::create([
                'email' => $email,
                'risk_score' => 50,
                'risk_factors' => ['fake_order'],
                'detection_method' => 'manual',
                'reason' => 'Flagged for fake order: ' . ($orderDetails['reason'] ?? 'Suspicious order pattern'),
            ]);
        }

        $suspiciousCustomer->addFakeOrder();
        
        // Auto-ban if too many fake orders
        if ($suspiciousCustomer->fake_order_count >= 3) {
            $suspiciousCustomer->ban('Auto-banned due to multiple fake orders', now()->addDays(30));
        }

        return $suspiciousCustomer;
    }

    public function flagCancelledOrder($email)
    {
        $suspiciousCustomer = SuspiciousCustomer::where('email', $email)->first();
        
        if ($suspiciousCustomer) {
            $suspiciousCustomer->addCancelledOrder();
        }

        return $suspiciousCustomer;
    }

    private function checkEmailDomain($email, &$riskFactors)
    {
        $score = 0;
        $domain = substr(strrchr($email, "@"), 1);

        // Check for suspicious email domains
        $suspiciousDomains = [
            'tempmail.org', '10minutemail.com', 'guerrillamail.com',
            'mailinator.com', 'yopmail.com', 'throwaway.email'
        ];

        if (in_array($domain, $suspiciousDomains)) {
            $score += 25;
            $riskFactors[] = 'temporary_email';
        }

        // Check for numeric-heavy emails
        if (preg_match('/\d{4,}/', $email)) {
            $score += 10;
            $riskFactors[] = 'numeric_heavy_email';
        }

        // Check for random looking emails
        if (preg_match('/^[a-z]+\d+[a-z]*@[a-z]+\.[a-z]{2,3}$/', $email)) {
            $score += 15;
            $riskFactors[] = 'random_pattern_email';
        }

        return $score;
    }

    private function checkPhonePattern($phone, &$riskFactors)
    {
        if (!$phone) return 0;

        $score = 0;

        // Check for invalid phone patterns
        if (strlen(preg_replace('/\D/', '', $phone)) < 10) {
            $score += 15;
            $riskFactors[] = 'invalid_phone_length';
        }

        // Check for suspicious patterns
        if (preg_match('/^123|^555|^000/', $phone)) {
            $score += 20;
            $riskFactors[] = 'suspicious_phone_pattern';
        }

        return $score;
    }

    private function checkIPAddress($ipAddress, &$riskFactors)
    {
        if (!$ipAddress) return 0;

        $score = 0;

        // Check for multiple accounts from same IP
        $ipCount = SuspiciousCustomer::where('ip_address', $ipAddress)->count();
        if ($ipCount >= 3) {
            $score += 20;
            $riskFactors[] = 'multiple_accounts_same_ip';
        }

        // Check for VPN/Proxy (basic check)
        $privateRanges = ['10.', '192.168.', '172.16.', '127.'];
        foreach ($privateRanges as $range) {
            if (strpos($ipAddress, $range) === 0) {
                $score += 5;
                $riskFactors[] = 'private_ip';
                break;
            }
        }

        return $score;
    }

    private function checkExistingUserBehavior($email, &$riskFactors)
    {
        $score = 0;
        $user = User::where('email', $email)->first();

        if ($user) {
            // Check account age
            if ($user->created_at->diffInDays(now()) < 1) {
                $score += 10;
                $riskFactors[] = 'very_new_account';
            }

            // Check if user has been previously flagged
            if ($user->is_active == false) {
                $score += 30;
                $riskFactors[] = 'previously_inactive';
            }
        }

        return $score;
    }

    private function checkOrderHistory($email, &$riskFactors)
    {
        $score = 0;
        $user = User::where('email', $email)->first();

        if ($user) {
            $orders = $user->orders;
            
            // Check cancellation rate
            if ($orders->count() > 0) {
                $cancelledCount = $orders->where('status', 'cancelled')->count();
                $cancellationRate = ($cancelledCount / $orders->count()) * 100;

                if ($cancellationRate >= 75) {
                    $score += 25;
                    $riskFactors[] = 'high_cancellation_rate';
                } elseif ($cancellationRate >= 50) {
                    $score += 15;
                    $riskFactors[] = 'medium_cancellation_rate';
                }
            }

            // Check for rapid ordering
            $recentOrders = $orders->where('created_at', '>', now()->subHours(1));
            if ($recentOrders->count() >= 3) {
                $score += 20;
                $riskFactors[] = 'rapid_ordering';
            }
        }

        return $score;
    }

    private function checkNamePattern($name, &$riskFactors)
    {
        if (!$name) return 0;

        $score = 0;

        // Check for random looking names
        if (preg_match('/^[A-Z]{3,}$/', $name)) {
            $score += 10;
            $riskFactors[] = 'all_caps_name';
        }

        // Check for single character names
        if (strlen(trim($name)) <= 2) {
            $score += 15;
            $riskFactors[] = 'suspicious_short_name';
        }

        // Check for numeric names
        if (preg_match('/\d/', $name)) {
            $score += 10;
            $riskFactors[] = 'numeric_in_name';
        }

        return $score;
    }

    public function isCustomerBanned($email)
    {
        $suspiciousCustomer = SuspiciousCustomer::where('email', $email)->first();
        
        return $suspiciousCustomer && $suspiciousCustomer->isCurrentlyBanned();
    }

    public function getCustomerRiskLevel($email)
    {
        $suspiciousCustomer = SuspiciousCustomer::where('email', $email)->first();
        
        if (!$suspiciousCustomer) {
            return 'low';
        }

        return $suspiciousCustomer->risk_level;
    }
}
