<?php

namespace Database\Factories;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiRequestLog>
 */
class ApiRequestLogFactory extends Factory
{
    protected $model = ApiRequestLog::class;

    public function definition(): array
    {
        return [
            'api_client_id' => ApiClient::factory(),
            'api_key_id' => ApiKey::factory(),
            'endpoint' => '/api/v1/'.fake()->randomElement(['products', 'orders', 'users']),
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'status_code' => fake()->randomElement([200, 201, 400, 401, 429, 500]),
            'response_time_ms' => fake()->numberBetween(5, 500),
            'rate_limit_limit' => 60,
            'rate_limit_remaining' => fake()->numberBetween(0, 60),
            'was_blocked' => false,
            'ip_address' => fake()->ipv4(),
            'requested_at' => now(),
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_code' => 429,
            'was_blocked' => true,
            'rate_limit_remaining' => 0,
        ]);
    }
}
