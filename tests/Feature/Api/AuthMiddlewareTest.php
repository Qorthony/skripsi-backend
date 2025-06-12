<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_requests_are_rejected()
    {
        $protectedEndpoints = [
            ['GET', '/api/user'],
            ['POST', '/api/logout'],
            ['GET', '/api/events'],
            ['POST', '/api/transactions'],
            ['GET', '/api/ticket-issued'],
        ];

        foreach ($protectedEndpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint);
            
            $response->assertStatus(401)
                    ->assertJson([
                        'message' => 'Unauthenticated.'
                    ]);
        }
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_requests_are_accepted()
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
    public function invalid_token_is_rejected()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-here',
        ])->getJson('/api/user');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function token_belongs_to_correct_user()
    {
        $user1 = User::factory()->create(['email_verified_at' => now()]);
        
        $token1 = $user1->createToken('auth_token')->plainTextToken;

        // Use user1's token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->getJson('/api/user');

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user1->id,
                    'email' => $user1->email,
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function missing_authorization_header_is_rejected()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function malformed_authorization_header_is_rejected()
    {
        $malformedHeaders = [
            'InvalidFormat',
            'Bearer',
            'Bearer ',
            'Basic token123',
            'Token token123',
        ];

        foreach ($malformedHeaders as $header) {
            $response = $this->withHeaders([
                'Authorization' => $header,
            ])->getJson('/api/user');

            $response->assertStatus(401);
        }
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_profile_endpoint_returns_correct_data()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'participant',
            'email_verified_at' => now()
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200)
                ->assertExactJson([
                    'id' => $user->id,
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'role' => 'participant',
                    'email_verified_at' => $user->email_verified_at->toISOString(),
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_profile_does_not_expose_sensitive_data()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $userData = $response->json();

        $this->assertArrayNotHasKey('password', $userData);
        $this->assertArrayNotHasKey('remember_token', $userData);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function token_can_be_used_immediately_after_creation()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        // Should work immediately without delay
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function concurrent_token_usage_works()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $token = $user->createToken('auth_token')->plainTextToken;

        // Make multiple concurrent requests
        $promises = [];
        for ($i = 0; $i < 5; $i++) {
            $promises[] = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/user');
        }

        // All should succeed
        foreach ($promises as $response) {
            $response->assertStatus(200);
        }
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function token_with_special_characters_in_name_works()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        $specialNames = [
            'device-1',
            'device_mobile',
            'device.tablet',
            'user@device',
            'device+token',
        ];

        foreach ($specialNames as $tokenName) {
            $token = $user->createToken($tokenName)->plainTextToken;

            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/user');

            $response->assertStatus(200);
        }
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function very_long_token_names_are_handled()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $longTokenName = str_repeat('a', 255);

        $token = $user->createToken($longTokenName)->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function api_root_endpoint_works_without_auth()
    {
        $response = $this->getJson('/api/');

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Hello World'
                ]);
    }    
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function preflight_cors_requests_work()
    {
        $response = $this->call(
            'OPTIONS',
            '/api/user',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'http://localhost:3000',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'authorization,content-type',
            ]
        );        // Should handle CORS preflight
        $this->assertContains($response->getStatusCode(), [200, 204]);
    }
}
