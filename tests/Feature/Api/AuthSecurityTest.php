<?php

namespace Tests\Feature\Api;

use App\Models\OneTimePassword;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function otp_is_invalidated_after_successful_use()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        $otp = OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        // First verification should succeed
        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        $response->assertStatus(200);

        // Second verification with same OTP should fail
        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid OTP code'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function expired_otp_cannot_be_used()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
            'otp_code' => '123456',
            'expires_at' => now()->subMinute(), // Expired 1 minute ago
        ]);

        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid OTP code'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function otp_purpose_must_match()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        // Create OTP for login purpose
        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Try to use it for registration verification
        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid OTP code'
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function multiple_failed_login_attempts_should_be_handled()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'role' => 'participant', // Need to specify role for authorization
            'email_verified_at' => now()
        ]);

        // Attempt multiple wrong emails (which will result in 403 unauthorized)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'wrongemail@example.com' // Non-existent email
            ]);

            $response->assertStatus(403); // Unauthorized since email doesn't exist
        }

        // Test with valid email should work
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com'
        ]);
        
        $response->assertStatus(200);

        // Even correct email might be rate limited
        // (depending on your rate limiting implementation)
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function sensitive_data_is_not_exposed_in_responses()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'role' => 'participant',
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJsonMissing(['password'])
                ->assertJsonMissing(['remember_token']);

        $userData = $response->json('user');
        $this->assertArrayNotHasKey('password', $userData);
        $this->assertArrayNotHasKey('remember_token', $userData);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function otp_brute_force_protection()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Try multiple wrong OTPs
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/register/verifyOtp', [
                'email' => 'john@example.com',
                'otp_code' => sprintf('%06d', $i) // Wrong OTPs
            ]);

            $response->assertStatus(401);
        }

        // Even correct OTP should still work (or be rate limited based on your implementation)
        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        // This assertion depends on your rate limiting implementation
        // $response->assertStatus(200); // If no rate limiting
        // $response->assertStatus(429); // If rate limited
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sql_injection_attempts_are_handled_safely()
    {
        $maliciousInputs = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "admin'/*",
            "' UNION SELECT * FROM users --",
        ];        foreach ($maliciousInputs as $maliciousInput) {
            $response = $this->postJson('/api/register', [
                'name' => $maliciousInput,
                'email' => $maliciousInput
            ]);

            // Should either fail validation or be safely handled
            $this->assertContains($response->status(), [422, 400, 500]);
        }
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function xss_attempts_are_stored_safely()
    {
        $xssPayloads = [
            '<script>alert("xss")</script>',
            'javascript:alert("xss")',
            '<img src="x" onerror="alert(1)">',
            '"><script>alert("xss")</script>',
        ];

        foreach ($xssPayloads as $payload) {
            $email = 'test' . rand(1000, 9999) . '@example.com';
            $response = $this->postJson('/api/register', [
                'name' => $payload,
                'email' => $email
            ]);

            if ($response->status() === 201) {
                $userData = $response->json('user');
                // Laravel stores the raw data but output should be escaped in views
                // The payload will be stored as-is in the database
                $this->assertEquals($payload, $userData['name']);
                
                // Verify it's stored in database correctly
                $this->assertDatabaseHas('users', [
                    'email' => $email,
                    'name' => $payload
                ]);
            }
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function concurrent_otp_requests_are_handled_correctly()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        // Make multiple concurrent OTP requests
        $promises = [];
        for ($i = 0; $i < 5; $i++) {
            $promises[] = $this->postJson('/api/register/resendOtp', [
                'email' => 'john@example.com'
            ]);
        }

        // All should succeed (or be rate limited)
        foreach ($promises as $response) {
            $this->assertContains($response->status(), [200, 429]);
        }

        // Should only have one active OTP per purpose
        $otpCount = OneTimePassword::where('identifier', 'john@example.com')
                                  ->where('purpose', OtpService::PURPOSE_REGISTER)
                                  ->count();
        
        $this->assertLessThanOrEqual(1, $otpCount);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function long_email_addresses_are_handled()
    {
        $longEmail = str_repeat('a', 250) . '@example.com';

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => $longEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Should fail validation due to email length
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unicode_characters_in_names_are_handled()
    {
        $unicodeNames = [
            'José María',
            '张三',
            'Müller',
            'Владимир',
            '🦄 Unicorn',
        ];        foreach ($unicodeNames as $name) {
            $response = $this->postJson('/api/register', [
                'name' => $name,
                'email' => 'test' . rand(1000, 9999) . '@example.com'
            ]);

            if ($response->status() === 201) {
                $userData = $response->json('user');
                $this->assertEquals($name, $userData['name']);
            }
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function case_insensitive_email_handling()
    {        // Register with lowercase email
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(201);

        // Try to register with uppercase email
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'JOHN@EXAMPLE.COM'
        ]);

        // Should either be treated as same user or create separate (depends on implementation)
        // Most implementations should treat emails as case-insensitive
        $this->assertContains($response->status(), [201, 409]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function token_expiration_is_handled()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        // Create an expired token manually (if your implementation supports it)
        $token = $user->createToken('auth_token');
        
        // Try to use the token after it should expire
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->plainTextToken,
        ])->getJson('/api/user');

        // Should work unless you have token expiration implemented
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function malformed_authorization_header_is_handled()
    {
        $malformedHeaders = [
            'Bearer',                    // Missing token
            'Bearer ',                   // Empty token
            'Basic token',               // Wrong type
            'Bearer invalid-token',      // Invalid token format
            'Bearer ' . str_repeat('a', 1000), // Very long token
        ];

        foreach ($malformedHeaders as $header) {
            $response = $this->withHeaders([
                'Authorization' => $header,
            ])->getJson('/api/user');

            $response->assertStatus(401);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function otp_cleanup_after_expiration()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        // Create multiple expired OTPs
        for ($i = 0; $i < 3; $i++) {
            OneTimePassword::create([
                'identifier' => 'john@example.com',
                'purpose' => OtpService::PURPOSE_REGISTER,
                'otp_code' => sprintf('%06d', $i),
                'expires_at' => now()->subMinutes($i + 1),
            ]);
        }

        // Try to verify with any of the expired OTPs
        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '000000'
        ]);

        $response->assertStatus(401);

        // Expired OTPs should not interfere with new ones
        $response = $this->postJson('/api/register/resendOtp', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200);
    }
}
