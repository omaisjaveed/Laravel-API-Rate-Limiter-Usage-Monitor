<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\ApiRequestLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_view_overview(): void
    {
        $this->get(route('dashboard.overview'))->assertRedirect('/login');
    }

    public function test_admin_sees_overview_metrics(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create(['name' => 'Chart Client', 'is_active' => true]);

        ApiRequestLog::factory()->create([
            'api_client_id' => $client->id,
            'status_code' => 200,
            'was_blocked' => false,
            'endpoint' => '/api/v1/products',
            'requested_at' => now(),
        ]);

        ApiRequestLog::factory()->blocked()->create([
            'api_client_id' => $client->id,
            'endpoint' => '/api/v1/orders',
            'requested_at' => now(),
        ]);

        ApiRequestLog::factory()->create([
            'api_client_id' => $client->id,
            'status_code' => 500,
            'was_blocked' => false,
            'endpoint' => '/api/v1/products',
            'requested_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.overview'))
            ->assertOk()
            ->assertSee('Total requests')
            ->assertSee('Chart Client')
            ->assertSee('/api/v1/products')
            ->assertSee('Request activity');
    }
}
