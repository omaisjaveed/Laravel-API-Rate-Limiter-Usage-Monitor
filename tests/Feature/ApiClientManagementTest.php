<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiClientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_manage_clients(): void
    {
        $this->get(route('dashboard.clients.index'))->assertRedirect('/login');
        $this->get(route('dashboard.clients.create'))->assertRedirect('/login');
    }

    public function test_admin_can_view_clients_index(): void
    {
        $user = User::factory()->create();
        ApiClient::factory()->count(2)->create();

        $this->actingAs($user)
            ->get(route('dashboard.clients.index'))
            ->assertOk()
            ->assertSee('API Clients');
    }

    public function test_admin_can_create_client_with_independent_limits(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.clients.store'), [
            'name' => 'Acme Payments',
            'description' => 'Checkout API access',
            'is_active' => 1,
            'rate_limit_per_minute' => 30,
            'rate_limit_per_hour' => 500,
            'rate_limit_per_day' => 5000,
        ]);

        $response->assertRedirect(route('dashboard.clients.index'));

        $this->assertDatabaseHas('api_clients', [
            'name' => 'Acme Payments',
            'is_active' => 1,
            'rate_limit_per_minute' => 30,
            'rate_limit_per_hour' => 500,
            'rate_limit_per_day' => 5000,
        ]);
    }

    public function test_client_create_validation_requires_name_and_limits(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.clients.store'), [])
            ->assertSessionHasErrors([
                'name',
                'rate_limit_per_minute',
                'rate_limit_per_hour',
                'rate_limit_per_day',
            ]);
    }

    public function test_admin_can_update_client(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create([
            'name' => 'Old Name',
            'rate_limit_per_minute' => 10,
        ]);

        $this->actingAs($user)
            ->put(route('dashboard.clients.update', $client), [
                'name' => 'New Name',
                'description' => 'Updated',
                'is_active' => 1,
                'rate_limit_per_minute' => 90,
                'rate_limit_per_hour' => 900,
                'rate_limit_per_day' => 9000,
            ])
            ->assertRedirect(route('dashboard.clients.index'));

        $this->assertDatabaseHas('api_clients', [
            'id' => $client->id,
            'name' => 'New Name',
            'rate_limit_per_minute' => 90,
        ]);
    }

    public function test_admin_can_activate_and_deactivate_client(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->patch(route('dashboard.clients.toggle', $client))
            ->assertRedirect(route('dashboard.clients.index'));

        $this->assertFalse($client->fresh()->is_active);

        $this->actingAs($user)
            ->patch(route('dashboard.clients.toggle', $client))
            ->assertRedirect(route('dashboard.clients.index'));

        $this->assertTrue($client->fresh()->is_active);
    }

    public function test_client_list_can_filter_by_status(): void
    {
        $user = User::factory()->create();
        ApiClient::factory()->create(['name' => 'Active Co', 'is_active' => true]);
        ApiClient::factory()->inactive()->create(['name' => 'Inactive Co']);

        $this->actingAs($user)
            ->get(route('dashboard.clients.index', ['status' => 'inactive']))
            ->assertOk()
            ->assertSee('Inactive Co')
            ->assertDontSee('Active Co');
    }
}
