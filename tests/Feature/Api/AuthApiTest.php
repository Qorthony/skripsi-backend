<?php

namespace Tests\Feature\Api;

use App\Models\OneTimePassword;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'OTP code has been sent to your email',
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role'
                    ]
                ]);

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'participant',
            'email_verified_at' => null
        ]);

        // Verify OTP was created
        $this->assertDatabaseHas('one_time_passwords', [
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
        ]);

        // Verify notification was sent
        $user = User::where('email', 'john@example.com')->first();
        Notification::assertSentTo($user, \App\Notifications\SendOtp::class);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_fails_with_invalid_email()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_fails_with_missing_name()
    {
        $userData = [
            'email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_fails_with_long_name()
    {
        $userData = [
            'name' => str_repeat('a', 256), // Too long (max 255)
            'email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_fails_with_already_verified_email()
    {
        // Create user with verified email
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now()
        ]);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(409)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Email already registered'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_resend_registration_otp()
    {
        // Create unverified user
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        $response = $this->postJson('/api/register/resendOtp', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'OTP code has been resent to your email'
                ]);

        // Verify new OTP was created
        $this->assertDatabaseHas('one_time_passwords', [
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
        ]);

        Notification::assertSentTo($user, \App\Notifications\SendOtp::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function resend_registration_otp_fails_for_non_existent_email()
    {
        $response = $this->postJson('/api/register/resendOtp', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Email not registered'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function resend_registration_otp_fails_for_already_verified_email()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/register/resendOtp', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(409)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Email already verified'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_verify_registration_otp()
    {
        // Create unverified user
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        // Create OTP
        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'OTP code is valid'
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user',
                    'token'
                ]);

        // Verify user email is now verified
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        // Verify OTP was deleted
        $this->assertDatabaseMissing('one_time_passwords', [
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_otp_verification_fails_with_invalid_otp()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_REGISTER,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '999999' // Wrong OTP
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid OTP code'
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_login_with_valid_email()
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
                ->assertJson([
                    'status' => 'success',
                    'message' => 'OTP code has been sent to your email'
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user'
                ]);

        // Verify OTP was created
        $this->assertDatabaseHas('one_time_passwords', [
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
        ]);

        Notification::assertSentTo($user, \App\Notifications\SendOtp::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_fails_with_non_existent_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'This action is unauthorized.'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_fails_with_organizer_role_mismatch()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'role' => 'organizer',
            'email_verified_at' => now()
        ]);

        // Try to login as participant when user is organizer
        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'as' => 'participant'
        ]);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'This action is unauthorized.'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_resend_login_otp()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/login/resendOtp', [
            'email' => 'john@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'OTP code has been resent to your email'
                ]);

        $this->assertDatabaseHas('one_time_passwords', [
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
        ]);

        Notification::assertSentTo($user, \App\Notifications\SendOtp::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function resend_login_otp_fails_for_non_existent_email()
    {
        $response = $this->postJson('/api/login/resendOtp', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Email not registered'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_verify_login_otp()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now()
        ]);

        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/login/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '123456'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'OTP code is valid'
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role'
                    ],
                    'token'
                ]);

        // Verify OTP was deleted after use
        $this->assertDatabaseMissing('one_time_passwords', [
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_otp_verification_fails_with_invalid_otp()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now()
        ]);

        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/login/verifyOtp', [
            'email' => 'john@example.com',
            'otp_code' => '999999'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Invalid OTP code'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_otp_verification_fails_with_expired_otp()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now()
        ]);

        OneTimePassword::create([
            'identifier' => 'john@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
            'otp_code' => '123456',
            'expires_at' => now()->subMinute(), // Expired
        ]);

        $response = $this->postJson('/api/login/verifyOtp', [
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
    public function organizer_user_login_includes_organizer_data()
    {
        $user = User::factory()->create([
            'email' => 'organizer@example.com',
            'role' => 'organizer',
            'email_verified_at' => now()
        ]);

        $organizer = \App\Models\Organizer::factory()->create([
            'user_id' => $user->id
        ]);

        OneTimePassword::create([
            'identifier' => 'organizer@example.com',
            'purpose' => OtpService::PURPOSE_LOGIN,
            'otp_code' => '123456',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/login/verifyOtp', [
            'email' => 'organizer@example.com',
            'otp_code' => '123456'
        ]);        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'organizer' => [
                            'id',
                            'nama' // Changed from 'nama_perusahaan' to 'nama'
                        ]
                    ],
                    'token'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'User logged out successfully'
                ]);

        // Verify token was revoked
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_access_protected_routes()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function api_requires_json_content_type()
    {
        $response = $this->post('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Should still work but prefer JSON
        $response->assertStatus(201);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function api_handles_malformed_json()
    {
        $response = $this->call(
            'POST',
            '/api/register',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"invalid": json'
        );

        // Laravel returns 302 redirect when validation fails in non-JSON request
        $response->assertStatus(302);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function registration_updates_existing_unverified_user()
    {
        // Create unverified user
        $existingUser = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);        $userData = [
            'name' => 'New Name', 
            'email' => 'john@example.com'
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);

        // Verify user was updated, not created new
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'name' => 'New Name',
            'email' => 'john@example.com',
        ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function otp_validation_requires_correct_fields()
    {
        $response = $this->postJson('/api/register/verifyOtp', [
            'email' => 'invalid-email'
            // Missing otp_code
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'otp_code']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function otp_verification_fails_with_invalid_format()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => null
        ]);

        // Test with various invalid OTP formats - they should all return 401 (Invalid OTP)
        $testCases = [
            '12345',     // Too short
            '1234567',   // Too long
            'abcdef',    // Not numeric
            '12ab56',    // Mixed
        ];

        foreach ($testCases as $invalidOtp) {
            $response = $this->postJson('/api/register/verifyOtp', [
                'email' => 'john@example.com',
                'otp_code' => $invalidOtp
            ]);

            // OTP validation happens at service level, returns 401 for invalid OTP
            $response->assertStatus(401)
                    ->assertJson([
                        'status' => 'error',
                        'message' => 'Invalid OTP code'
                    ]);
        }
    }
}
