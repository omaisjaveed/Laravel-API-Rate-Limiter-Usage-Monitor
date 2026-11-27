<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use App\Models\User;
use App\Services\ApiRateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RateLimitAndViolationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_admin_can_view_rate_limit_settings_with_usage(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create([
            'name' => 'Usage Client',
            'rate_limit_per_minute' => 10,
        ]);
        $plain = ApiKey::generateFor($client)['plain_text'];

        $this->withToken($plain)->getJson('/api/v1/products')->assertOk();

        $this->actingAs($user)
            ->get(route('dashboard.rate-limits.index'))
            ->assertOk()
            ->assertSee('Usage Client')
            ->assertSee('Per minute');
    }

    public function test_admin_can_update_client_rate_limits(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create([
            'rate_limit_per_minute' => 60,
            'rate_limit_per_hour' => 1000,
            'rate_limit_per_day' => 10000,
        ]);

        $this->actingAs($user)
            ->put(route('dashboard.rate-limits.update', $client), [
                'rate_limit_per_minute' => 25,
                'rate_limit_per_hour' => 400,
                'rate_limit_per_day' => 4000,
            ])
            ->assertRedirect(route('dashboard.rate-limits.index'));

        $this->assertDatabaseHas('api_clients', [
            'id' => $client->id,
            'rate_limit_per_minute' => 25,
            'rate_limit_per_hour' => 400,
            'rate_limit_per_day' => 4000,
        ]);
    }

    public function test_current_usage_reader_does_not_consume_quota(): void
    {
        $client = ApiClient::factory()->create([
            'rate_limit_per_minute' => 5,
            'rate_limit_per_hour' => 100,
            'rate_limit_per_day' => 1000,
        ]);
        $plain = ApiKey::generateFor($client)['plain_text'];

        $this->withToken($plain)->getJson('/api/v1/products')->assertOk();

        $limiter = app(ApiRateLimiter::class);
        $first = $limiter->currentUsage($client);
        $second = $limiter->currentUsage($client);

        $this->assertSame(1, $first['minute']['used']);
        $this->assertSame(1, $second['minute']['used']);
        $this->assertSame(4, $second['minute']['remaining']);
    }

    public function test_admin_can_view_violations_page(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create(['name' => 'Blocked Co']);

        ApiRequestLog::factory()->blocked()->create([
            'api_client_id' => $client->id,
            'endpoint' => '/api/v1/products',
            'method' => 'GET',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.violations.index'))
            ->assertOk()
            ->assertSee('Blocked Co')
            ->assertSee('/api/v1/products')
            ->assertSee('Total violations');
    }

    public function test_guests_cannot_open_rate_limits_or_violations(): void
    {
        $this->get(route('dashboard.rate-limits.index'))->assertRedirect('/login');
        $this->get(route('dashboard.violations.index'))->assertRedirect('/login');
    }
}
