<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_dashboard_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_admin_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard.overview'));

        $this->assertAuthenticated();
    }

    public function test_authenticated_admin_can_view_dashboard_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk();

        $this->actingAs($user)->get(route('dashboard.clients.index'))->assertOk();
        $this->actingAs($user)->get(route('dashboard.keys.index'))->assertOk();
        $this->actingAs($user)->get(route('dashboard.rate-limits.index'))->assertOk();
        $this->actingAs($user)->get(route('dashboard.logs.index'))->assertOk();
        $this->actingAs($user)->get(route('dashboard.violations.index'))->assertOk();
    }

    public function test_admin_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
