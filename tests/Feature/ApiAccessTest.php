<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_missing_api_key_is_rejected(): void
    {
        $this->getJson('/api/v1/products')
            ->assertUnauthorized()
            ->assertJsonPath('error', 'unauthenticated');
    }

    public function test_invalid_api_key_is_rejected(): void
    {
        $this->withToken('rl_this_key_does_not_exist_anywhere_1234567890')
            ->getJson('/api/v1/products')
            ->assertUnauthorized();
    }

    public function test_revoked_api_key_is_rejected(): void
    {
        $client = ApiClient::factory()->create();
        $generated = ApiKey::generateFor($client, 'Revoked');
        $generated['model']->revoke();

        $this->withToken($generated['plain_text'])
            ->getJson('/api/v1/products')
            ->assertUnauthorized()
            ->assertJsonFragment(['message' => 'API key has been revoked.']);
    }

    public function test_inactive_client_is_rejected(): void
    {
        $client = ApiClient::factory()->inactive()->create();
        $generated = ApiKey::generateFor($client, 'Inactive client key');

        $this->withToken($generated['plain_text'])
            ->getJson('/api/v1/products')
            ->assertForbidden();
    }

    public function test_valid_api_key_can_access_products(): void
    {
        $client = ApiClient::factory()->create(['name' => 'Shop API']);
        $generated = ApiKey::generateFor($client, 'Prod');

        $response = $this->withToken($generated['plain_text'])
            ->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('meta.client', 'Shop API')
            ->assertJsonStructure(['data', 'meta']);

        $response->assertHeader('X-RateLimit-Limit')
            ->assertHeader('X-RateLimit-Remaining')
            ->assertHeader('X-RateLimit-Reset');
    }

    public function test_valid_api_key_can_access_orders(): void
    {
        $client = ApiClient::factory()->create();
        $generated = ApiKey::generateFor($client);

        $this->withToken($generated['plain_text'])
            ->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_valid_api_key_can_create_update_and_delete_product(): void
    {
        $client = ApiClient::factory()->create();
        $token = ApiKey::generateFor($client)['plain_text'];

        $this->withToken($token)
            ->postJson('/api/v1/products', [
                'name' => 'Gaming Headset',
                'price' => 59.99,
                'currency' => 'USD',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Gaming Headset');

        $this->withToken($token)
            ->putJson('/api/v1/products/1', [
                'name' => 'Gaming Headset Pro',
                'price' => 69.99,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Gaming Headset Pro');

        $this->withToken($token)
            ->deleteJson('/api/v1/products/1')
            ->assertOk()
            ->assertJsonPath('data.deleted', true);
    }

    public function test_valid_api_key_can_create_update_and_delete_order(): void
    {
        $client = ApiClient::factory()->create();
        $token = ApiKey::generateFor($client)['plain_text'];

        $this->withToken($token)
            ->postJson('/api/v1/orders', [
                'total' => 150,
                'status' => 'pending',
            ])
            ->assertCreated();

        $this->withToken($token)
            ->putJson('/api/v1/orders/1001', [
                'status' => 'paid',
                'total' => 150,
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'paid');

        $this->withToken($token)
            ->deleteJson('/api/v1/orders/1001')
            ->assertOk()
            ->assertJsonPath('data.deleted', true);
    }

    public function test_rate_limit_returns_429_with_headers(): void
    {
        $client = ApiClient::factory()->create([
            'rate_limit_per_minute' => 2,
            'rate_limit_per_hour' => 100,
            'rate_limit_per_day' => 1000,
        ]);
        $generated = ApiKey::generateFor($client);

        $this->withToken($generated['plain_text'])->getJson('/api/v1/products')->assertOk();
        $this->withToken($generated['plain_text'])->getJson('/api/v1/products')->assertOk();

        $blocked = $this->withToken($generated['plain_text'])->getJson('/api/v1/products');

        $blocked->assertStatus(429)
            ->assertJsonPath('error', 'rate_limit_exceeded')
            ->assertHeader('Retry-After')
            ->assertHeader('X-RateLimit-Remaining', '0')
            ->assertHeader('X-RateLimit-Limit');
    }

    public function test_rate_limits_are_isolated_per_client(): void
    {
        $clientA = ApiClient::factory()->create([
            'name' => 'Client A',
            'rate_limit_per_minute' => 1,
            'rate_limit_per_hour' => 100,
            'rate_limit_per_day' => 1000,
        ]);
        $clientB = ApiClient::factory()->create([
            'name' => 'Client B',
            'rate_limit_per_minute' => 1,
            'rate_limit_per_hour' => 100,
            'rate_limit_per_day' => 1000,
        ]);

        $keyA = ApiKey::generateFor($clientA)['plain_text'];
        $keyB = ApiKey::generateFor($clientB)['plain_text'];

        $this->withToken($keyA)->getJson('/api/v1/products')->assertOk();
        $this->withToken($keyA)->getJson('/api/v1/products')->assertStatus(429);

        $this->withToken($keyB)->getJson('/api/v1/products')->assertOk();
        $this->withToken($keyB)->getJson('/api/v1/products')->assertStatus(429);
    }

    public function test_dashboard_auth_is_separate_from_api_keys(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/v1/products')
            ->assertUnauthorized();
    }
}
