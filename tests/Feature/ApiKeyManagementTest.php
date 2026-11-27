<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_manage_keys(): void
    {
        $this->get(route('dashboard.keys.index'))->assertRedirect('/login');
        $this->get(route('dashboard.keys.create'))->assertRedirect('/login');
    }

    public function test_admin_can_generate_key_and_see_plain_text_once(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.keys.store'), [
            'api_client_id' => $client->id,
            'name' => 'Mobile app',
        ]);

        $response->assertRedirect(route('dashboard.keys.index', ['client' => $client->id]));
        $response->assertSessionHas('plain_api_key');

        $plain = session('plain_api_key');
        $this->assertNotEmpty($plain);
        $this->assertStringStartsWith('rl_', $plain);

        $this->assertDatabaseCount('api_keys', 1);
        $key = ApiKey::query()->first();
        $this->assertSame(hash('sha256', $plain), $key->key_hash);
        $this->assertDatabaseMissing('api_keys', ['key_hash' => $plain]);
    }

    public function test_raw_key_is_not_persisted_in_database(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create();

        $this->actingAs($user)->post(route('dashboard.keys.store'), [
            'api_client_id' => $client->id,
            'name' => 'Secret check',
        ]);

        $plain = session('plain_api_key');
        $row = ApiKey::query()->first();

        $this->assertNotSame($plain, $row->key_hash);
        $this->assertSame(64, strlen($row->key_hash));
    }

    public function test_admin_can_revoke_key(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create();
        $key = ApiKey::factory()->create(['api_client_id' => $client->id]);

        $this->actingAs($user)
            ->patch(route('dashboard.keys.revoke', $key))
            ->assertRedirect(route('dashboard.keys.index', ['client' => $client->id]));

        $this->assertNotNull($key->fresh()->revoked_at);
        $this->assertFalse($key->fresh()->isUsable());
    }

    public function test_admin_can_regenerate_key(): void
    {
        $user = User::factory()->create();
        $client = ApiClient::factory()->create();
        $oldHash = hash('sha256', 'rl_old_secret_value_for_test_123456789012345678');
        $key = ApiKey::factory()->create([
            'api_client_id' => $client->id,
            'key_prefix' => 'rl_old_secre',
            'key_hash' => $oldHash,
        ]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.keys.regenerate', $key));

        $response->assertRedirect(route('dashboard.keys.index', ['client' => $client->id]));
        $response->assertSessionHas('plain_api_key');

        $key->refresh();
        $plain = session('plain_api_key');

        $this->assertNotSame($oldHash, $key->key_hash);
        $this->assertSame(hash('sha256', $plain), $key->key_hash);
        $this->assertNull($key->revoked_at);
    }

    public function test_key_generation_requires_client(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('dashboard.keys.store'), ['name' => 'Missing client'])
            ->assertSessionHasErrors(['api_client_id']);
    }

    public function test_revoked_key_cannot_be_looked_up_as_usable(): void
    {
        $client = ApiClient::factory()->create();
        $generated = ApiKey::generateFor($client, 'Temp');
        $generated['model']->revoke();

        $found = ApiKey::findByPlainText($generated['plain_text']);

        $this->assertNotNull($found);
        $this->assertTrue($found->isRevoked());
        $this->assertFalse($found->isUsable());
    }
}
