<?php

namespace Database\Factories;

use App\Models\ApiClient;
use App\Models\ApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ApiKey>
 */
class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    public function definition(): array
    {
        $plainText = 'rl_'.Str::random(48);

        return [
            'api_client_id' => ApiClient::factory(),
            'name' => fake()->optional()->words(2, true),
            'key_prefix' => substr($plainText, 0, 12),
            'key_hash' => hash('sha256', $plainText),
            'last_used_at' => null,
            'revoked_at' => null,
        ];
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'revoked_at' => now(),
        ]);
    }
}
