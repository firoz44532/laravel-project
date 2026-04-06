<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class FakeCustomerDetectionService
{
    private array $suspiciousEmailDomains = [
        '10minutemail.com', 'tempmail.org', 'guerrillamail.com', 'mailinator.com',
        'yopmail.com', 'temp-mail.org', 'throwaway.email', 'fakeemail.com',
        'maildrop.cc', 'tempmail.org', '20minutemail.com', 'getairmail.com',
        'mailinator.net', 'spamgourmet.com', 'meltmail.com', 'mytemp.email'
    ];

    private array $suspiciousNamePatterns = [
        '/^[a-z]{1,2}[0-9]+$/i',           // ab123, xy456
        '/^[0-9]+[a-z]{1,2}$/i',           // 123ab, 456xy
        '/^(test|demo|fake|sample|dummy)/i', // testuser, demo123
        '/^(user|customer|guest)[0-9]+$/i',  // user123, customer456
        '/^[a-z]{20,}$/i',                  // verylongstringwithoutspaces
        '/^(.)\1{5,}$/',                   // aaaaaaa, bbbbbbb
    ];

    private array $suspiciousPhonePatterns = [
        '/^000/',                           // Numbers starting with 000
        '/^123/',                           // Numbers starting with 123
        '/^(.)\1{9,}$/',                    // 1111111111, 2222222222
        '/^[0-9]{1,3}$/',                   // Too short numbers
        '/^555/',                           // Fake US numbers
        '/^999/',                           // Fake numbers
    ];

    public function detectFakeCustomer(Request $request, array $userData): array
    {
        // Validate input data
        if (!is_array($userData)) {
            throw new \InvalidArgumentException('User data must be an array');
        }

        $riskScore = 0;
        $flags = [];
        $ipAddress = $request->ip() ?: 'unknown';

        // Sanitize IP address for cache keys
        $safeIp = md5($ipAddress);

        // Email validation - only if email is provided
        if (!empty($userData['email'])) {
            $emailRisk = $this->validateEmail($userData['email']);
            $riskScore += $emailRisk['score'];
            $flags = array_merge($flags, $emailRisk['flags']);
        }

        // Phone validation
        if (!empty($userData['phone'])) {
            $phoneRisk = $this->validatePhone($userData['phone']);
            $riskScore += $phoneRisk['score'];
            $flags = array_merge($flags, $phoneRisk['flags']);
        }

        // Name validation - only if name is provided
        if (!empty($userData['name'])) {
            $nameRisk = $this->validateName($userData['name']);
            $riskScore += $nameRisk['score'];
            $flags = array_merge($flags, $nameRisk['flags']);
        }

        // IP-based analysis - only if email is provided
        if (!empty($userData['email'])) {
            $ipRisk = $this->analyzeIpAddress($ipAddress, $userData['email']);
            $riskScore += $ipRisk['score'];
            $flags = array_merge($flags, $ipRisk['flags']);
        }

        // Behavioral analysis - only if email is provided (consistent with IP analysis)
        if (!empty($userData['email'])) {
            $behaviorRisk = $this->analyzeBehavior($ipAddress);
            $riskScore += $behaviorRisk['score'];
            $flags = array_merge($flags, $behaviorRisk['flags']);
        }

        // Determine risk level
        $riskLevel = $this->getRiskLevel($riskScore);

        // Log suspicious registrations - only if we have meaningful data
        if ($riskLevel !== 'low' && !empty($userData['email'])) {
            $this->logSuspiciousRegistration($request, $userData, $riskScore, $flags, $riskLevel);
        }

        return [
            'is_fake' => $riskLevel === 'high',
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'flags' => $flags,
            'should_block' => $riskScore >= 15,
            'should_review' => $riskScore >= 8,
        ];
    }

    private function validateEmail(string $email): array
    {
        $score = 0;
        $flags = [];

        // Validate email format first
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $score += 5;
            $flags[] = 'invalid_email_format';
            return ['score' => $score, 'flags' => $flags];
        }

        $domain = strtolower(substr(strrchr($email, '@'), 1));

        // Check for disposable email domains
        if (in_array($domain, $this->suspiciousEmailDomains)) {
            $score += 8;
            $flags[] = 'disposable_email_domain';
        }

        // Check for suspicious email patterns
        if (preg_match('/^[0-9]+@/', $email)) {
            $score += 3;
            $flags[] = 'numeric_email_prefix';
        }

        if (preg_match('/^(test|demo|fake|sample|dummy)/', strtolower($email))) {
            $score += 5;
            $flags[] = 'suspicious_email_prefix';
        }

        // Check for random looking emails
        if (preg_match('/^[a-z]{1,2}[0-9]+@/', $email)) {
            $score += 2;
            $flags[] = 'random_email_pattern';
        }

        // Check for consecutive characters
        if (preg_match('/(.)\1{4,}@/', $email)) {
            $score += 3;
            $flags[] = 'repeated_chars_email';
        }

        return ['score' => $score, 'flags' => $flags];
    }

    private function validatePhone(string $phone): array
    {
        $score = 0;
        $flags = [];

        // Remove non-numeric characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Check suspicious patterns
        foreach ($this->suspiciousPhonePatterns as $pattern) {
            if (preg_match($pattern, $cleanPhone)) {
                $score += 4;
                $flags[] = 'suspicious_phone_pattern';
            }
        }

        // Check for obviously fake numbers
        if (in_array($cleanPhone, ['1234567890', '1111111111', '0000000000', '9999999999'])) {
            $score += 6;
            $flags[] = 'obviously_fake_phone';
        }

        // Check length (assuming typical phone numbers are 10-15 digits)
        if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
            $score += 2;
            $flags[] = 'invalid_phone_length';
        }

        return ['score' => $score, 'flags' => $flags];
    }

    private function validateName(string $name): array
    {
        $score = 0;
        $flags = [];

        // Check suspicious name patterns
        foreach ($this->suspiciousNamePatterns as $pattern) {
            if (preg_match($pattern, $name)) {
                $score += 4;
                $flags[] = 'suspicious_name_pattern';
                break;
            }
        }

        // Check for single character names
        if (strlen(trim($name)) <= 2) {
            $score += 3;
            $flags[] = 'too_short_name';
        }

        // Check for names with only numbers
        if (preg_match('/^[0-9]+$/', $name)) {
            $score += 6;
            $flags[] = 'numeric_name';
        }

        // Check for keyboard patterns
        if (preg_match('/^(qwerty|asdf|zxcv|1234|abcd)/i', $name)) {
            $score += 3;
            $flags[] = 'keyboard_pattern_name';
        }

        return ['score' => $score, 'flags' => $flags];
    }

    private function analyzeIpAddress(?string $ipAddress, string $email): array
    {
        $score = 0;
        $flags = [];
        $safeIp = md5($ipAddress);
        $cacheKey = "registrations_by_ip:" . $safeIp;

        // Get recent registrations from this IP
        $recentRegistrations = Cache::get($cacheKey, 0);
        
        // Check for multiple registrations from same IP
        if ($recentRegistrations >= 3) {
            $score += 6;
            $flags[] = 'multiple_registrations_same_ip';
        } elseif ($recentRegistrations >= 2) {
            $score += 3;
            $flags[] = 'few_registrations_same_ip';
        }

        // Check for existing accounts with same IP (simplified approach)
        // Note: This requires IP tracking in users table or separate tracking table
        $existingAccounts = 0; // Placeholder - implement based on your IP tracking strategy

        if ($existingAccounts > 0) {
            $score += 4;
            $flags[] = 'recent_account_same_ip';
        }

        // Increment counter for this IP
        Cache::put($cacheKey, $recentRegistrations + 1, now()->addHours(24));

        return ['score' => $score, 'flags' => $flags];
    }

    private function analyzeBehavior(?string $ipAddress): array
    {
        $score = 0;
        $flags = [];
        
        // Handle NULL IP address
        if (empty($ipAddress) || $ipAddress === 'unknown') {
            return ['score' => $score, 'flags' => $flags];
        }
        
        $safeIp = md5($ipAddress);
        $cacheKey = "behavior_analysis:" . $safeIp;

        $behavior = Cache::get($cacheKey, [
            'registration_attempts' => 0,
            'failed_attempts' => 0,
            'last_attempt' => null
        ]);

        // Check for rapid registration attempts
        if ($behavior['last_attempt'] instanceof \Carbon\Carbon && 
            $behavior['last_attempt']->diffInMinutes(now()) < 5) {
            $score += 5;
            $flags[] = 'rapid_registration_attempts';
        }

        // Check for high failure rate
        if ($behavior['failed_attempts'] > 3) {
            $score += 3;
            $flags[] = 'high_failure_rate';
        }

        // Update behavior data
        $behavior['registration_attempts']++;
        $behavior['last_attempt'] = now();
        Cache::put($cacheKey, $behavior, now()->addHours(24));

        return ['score' => $score, 'flags' => $flags];
    }

    private function getRiskLevel(int $score): string
    {
        if ($score >= 15) {
            return 'high';
        } elseif ($score >= 8) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    private function logSuspiciousRegistration(Request $request, array $userData, int $score, array $flags, string $level): void
    {
        $ipAddress = $request->ip() ?: 'unknown';
        
        Log::warning('Suspicious registration detected', [
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent() ?: 'unknown',
            'email' => $userData['email'],
            'name' => $userData['name'],
            'phone' => $userData['phone'] ?? null,
            'risk_score' => $score,
            'risk_level' => $level,
            'flags' => $flags,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function markRegistrationFailure(?string $ipAddress): void
    {
        if (empty($ipAddress) || $ipAddress === 'unknown') {
            return;
        }
        
        $safeIp = md5($ipAddress);
        $cacheKey = "behavior_analysis:" . $safeIp;
        $behavior = Cache::get($cacheKey, [
            'registration_attempts' => 0,
            'failed_attempts' => 0,
            'last_attempt' => null
        ]);

        $behavior['failed_attempts']++;
        Cache::put($cacheKey, $behavior, now()->addHours(24));
    }

    public function isBlockedIp(?string $ipAddress): bool
    {
        if (empty($ipAddress) || $ipAddress === 'unknown') {
            return false;
        }
        
        return Cache::has("blocked_ip:" . md5($ipAddress));
    }

    public function blockIp(?string $ipAddress, int $durationMinutes = 60): void
    {
        if (empty($ipAddress) || $ipAddress === 'unknown') {
            return;
        }
        
        Cache::put("blocked_ip:" . md5($ipAddress), true, now()->addMinutes($durationMinutes));
    }
}
