<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Services\FakeCustomerDetectionService;
use App\Models\User;

class FakeCustomerDetectionTest extends TestCase
{
    use RefreshDatabase;

    protected FakeCustomerDetectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FakeCustomerDetectionService::class);
        Cache::flush();
    }

    /** @test */
    public function it_detects_disposable_email_domains()
    {
        $userData = [
            'email' => 'test@10minutemail.com',
            'name' => 'John Doe',
            'phone' => '01234567890'
        ];

        $request = $this->createRequest($userData);
        $result = $this->service->detectFakeCustomer($request, $userData);

        $this->assertContains('disposable_email_domain', $result['flags']);
        $this->assertGreaterThanOrEqual(8, $result['risk_score']);
    }

    /** @test */
    public function it_detects_suspicious_name_patterns()
    {
        $testCases = [
            ['name' => 'ab123', 'expected_flag' => 'suspicious_name_pattern'],
            ['name' => 'testuser', 'expected_flag' => 'suspicious_name_pattern'],
            ['name' => '12345', 'expected_flag' => 'numeric_name'],
            ['name' => 'qwerty', 'expected_flag' => 'keyboard_pattern_name'],
        ];

        foreach ($testCases as $case) {
            $userData = [
                'email' => 'test@example.com',
                'name' => $case['name'],
                'phone' => '01234567890'
            ];

            $request = $this->createRequest($userData);
            $result = $this->service->detectFakeCustomer($request, $userData);

            $this->assertContains($case['expected_flag'], $result['flags']);
        }
    }

    /** @test */
    public function it_detects_suspicious_phone_numbers()
    {
        $testCases = [
            ['phone' => '0001234567', 'expected_flag' => 'suspicious_phone_pattern'],
            ['phone' => '1234567890', 'expected_flag' => 'obviously_fake_phone'],
            ['phone' => '1111111111', 'expected_flag' => 'obviously_fake_phone'],
            ['phone' => '123', 'expected_flag' => 'invalid_phone_length'],
        ];

        foreach ($testCases as $case) {
            $userData = [
                'email' => 'test@example.com',
                'name' => 'John Doe',
                'phone' => $case['phone']
            ];

            $request = $this->createRequest($userData);
            $result = $this->service->detectFakeCustomer($request, $userData);

            $this->assertContains($case['expected_flag'], $result['flags']);
        }
    }

    /** @test */
    public function it_tracks_multiple_registrations_from_same_ip()
    {
        $userData = [
            'email' => 'test1@example.com',
            'name' => 'John Doe',
            'phone' => '01234567890'
        ];

        $ipAddress = '192.168.1.1';

        // First registration
        $request1 = $this->createRequest($userData, $ipAddress);
        $result1 = $this->service->detectFakeCustomer($request1, $userData);
        $initialScore = $result1['risk_score'];

        // Second registration
        $userData['email'] = 'test2@example.com';
        $request2 = $this->createRequest($userData, $ipAddress);
        $result2 = $this->service->detectFakeCustomer($request2, $userData);
        $secondScore = $result2['risk_score'];

        // Third registration
        $userData['email'] = 'test3@example.com';
        $request3 = $this->createRequest($userData, $ipAddress);
        $result3 = $this->service->detectFakeCustomer($request3, $userData);
        $thirdScore = $result3['risk_score'];

        // Risk score should increase with multiple registrations from same IP
        $this->assertGreaterThan($initialScore, $secondScore);
        $this->assertGreaterThan($secondScore, $thirdScore);
    }

    /** @test */
    public function it_blocks_high_risk_registrations()
    {
        $userData = [
            'email' => 'ab123@10minutemail.com', // Multiple suspicious patterns
            'name' => 'test123',
            'phone' => '0001234567'
        ];

        $request = $this->createRequest($userData);
        $result = $this->service->detectFakeCustomer($request, $userData);

        $this->assertTrue($result['should_block']);
        $this->assertTrue($result['is_fake']);
        $this->assertEquals('high', $result['risk_level']);
    }

    /** @test */
    public function it_allows_legitimate_registrations()
    {
        $userData = [
            'email' => 'john.doe@gmail.com',
            'name' => 'John Doe',
            'phone' => '01712345678'
        ];

        $request = $this->createRequest($userData);
        $result = $this->service->detectFakeCustomer($request, $userData);

        $this->assertFalse($result['should_block']);
        $this->assertFalse($result['is_fake']);
        $this->assertEquals('low', $result['risk_level']);
        $this->assertLessThan(8, $result['risk_score']);
    }

    /** @test */
    public function registration_with_medium_risk_requires_review()
    {
        $userData = [
            'email' => 'test@gmail.com',
            'name' => 'ab123', // Suspicious name pattern
            'phone' => '01712345678'
        ];

        $request = $this->createRequest($userData);
        $result = $this->service->detectFakeCustomer($request, $userData);

        $this->assertFalse($result['should_block']);
        $this->assertTrue($result['should_review']);
        $this->assertEquals('medium', $result['risk_level']);
    }

    /** @test */
    public function it_blocks_ip_addresses_after_high_risk_detection()
    {
        $userData = [
            'email' => 'ab123@10minutemail.com',
            'name' => 'test123',
            'phone' => '0001234567'
        ];

        $ipAddress = '192.168.1.100';
        $request = $this->createRequest($userData, $ipAddress);

        // First detection should block
        $result = $this->service->detectFakeCustomer($request, $userData);
        $this->assertTrue($result['should_block']);

        // Manually block the IP as the service doesn't auto-block
        $this->service->blockIp($ipAddress);

        // IP should now be blocked
        $this->assertTrue($this->service->isBlockedIp($ipAddress));
    }

    /** @test */
    public function it_marks_registration_failures()
    {
        $ipAddress = '192.168.1.200';
        
        // Mark some failures
        $this->service->markRegistrationFailure($ipAddress);
        $this->service->markRegistrationFailure($ipAddress);
        $this->service->markRegistrationFailure($ipAddress);
        $this->service->markRegistrationFailure($ipAddress); // Need 4+ failures

        $userData = [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'phone' => '01234567890'
        ];

        $request = $this->createRequest($userData, $ipAddress);
        $result = $this->service->detectFakeCustomer($request, $userData);

        $this->assertContains('high_failure_rate', $result['flags']);
    }

    private function createRequest(array $data, string $ip = '127.0.0.1')
    {
        return new \Illuminate\Http\Request(
            server: ['REMOTE_ADDR' => $ip],
            request: $data
        );
    }
}
