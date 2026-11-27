<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiUsageLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_successful_api_request_is_logged(): void
    {
        $client = ApiClient::factory()->create();
        $generated = ApiKey::generateFor($client, 'Logger');

        $this->withToken($generated['plain_text'])
            ->getJson('/api/v1/products')
            ->assertOk();

        $this->assertDatabaseCount('api_request_logs', 1);

        $log = ApiRequestLog::query()->first();

        $this->assertSame($client->id, $log->api_client_id);
        $this->assertSame($generated['model']->id, $log->api_key_id);
        $this->assertSame('/api/v1/products', $log->endpoint);
        $this->assertSame('GET', $log->method);
        $this->assertSame(200, $log->status_code);
        $this->assertFalse($log->was_blocked);
        $this->assertGreaterThanOrEqual(0, $log->response_time_ms);
        $this->assertNotNull($log->rate_limit_limit);
    }

    public function test_rate_limited_request_is_logged_as_blocked(): void
    {
        $client = ApiClient::factory()->create([
            'rate_limit_per_minute' => 1,
            'rate_limit_per_hour' => 100,
            'rate_limit_per_day' => 1000,
        ]);
        $plain = ApiKey::generateFor($client)['plain_text'];

        $this->withToken($plain)->getJson('/api/v1/products')->assertOk();
        $this->withToken($plain)->getJson('/api/v1/products')->assertStatus(429);

        $blocked = ApiRequestLog::query()->where('was_blocked', true)->first();

        $this->assertNotNull($blocked);
        $this->assertSame(429, $blocked->status_code);
        $this->assertSame($client->id, $blocked->api_client_id);
    }

    public function test_invalid_key_request_is_logged_without_client(): void
    {
        $this->withToken('rl_invalid_key_value_that_does_not_match_anything')
            ->getJson('/api/v1/products')
            ->assertUnauthorized();

        $log = ApiRequestLog::query()->first();

        $this->assertNotNull($log);
        $this->assertNull($log->api_client_id);
        $this->assertNull($log->api_key_id);
        $this->assertSame(401, $log->status_code);
        $this->assertFalse($log->was_blocked);
    }

    public function test_logs_never_store_authorization_header_or_raw_key(): void
    {
        $client = ApiClient::factory()->create();
        $plain = ApiKey::generateFor($client)['plain_text'];

        $this->withToken($plain)
            ->getJson('/api/v1/products?api_key='.$plain)
            ->assertOk();

        $log = ApiRequestLog::query()->first();
        $encoded = json_encode($log->toArray());

        $this->assertStringNotContainsString($plain, $encoded);
        $this->assertSame('/api/v1/products', $log->endpoint);
        $this->assertStringNotContainsString('api_key=', $log->endpoint);
    }

    public function test_admin_can_view_request_logs(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create(['name' => 'Logged Client']);
        ApiRequestLog::factory()->create([
            'api_client_id' => $client->id,
            'endpoint' => '/api/v1/orders',
            'method' => 'GET',
            'status_code' => 200,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.logs.index'))
            ->assertOk()
            ->assertSee('Logged Client')
            ->assertSee('/api/v1/orders');
    }
}
