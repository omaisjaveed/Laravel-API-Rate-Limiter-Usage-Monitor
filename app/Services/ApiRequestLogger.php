<?php

namespace App\Services;

use App\Models\ApiClient;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiRequestLogger
{
    public function log(Request $request, Response $response, float $startedAt): void
    {
        try {
            /** @var ApiClient|null $client */
            $client = $request->attributes->get('api_client');
            /** @var ApiKey|null $apiKey */
            $apiKey = $request->attributes->get('api_key');
            /** @var array<string, mixed>|null $rateLimit */
            $rateLimit = $request->attributes->get('rate_limit');

            $statusCode = $response->getStatusCode();
            $wasBlocked = $statusCode === 429;

            ApiRequestLog::query()->create([
                'api_client_id' => $client?->id,
                'api_key_id' => $apiKey?->id,
                'endpoint' => '/'.$request->path(),
                'method' => strtoupper($request->method()),
                'status_code' => $statusCode,
                'response_time_ms' => $this->elapsedMs($startedAt),
                'rate_limit_limit' => is_array($rateLimit) ? ($rateLimit['limit'] ?? null) : null,
                'rate_limit_remaining' => is_array($rateLimit) ? ($rateLimit['remaining'] ?? null) : null,
                'was_blocked' => $wasBlocked,
                'ip_address' => $request->ip(),
                'requested_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function elapsedMs(float $startedAt): int
    {
        return (int) max(0, round((hrtime(true) - $startedAt) / 1_000_000));
    }
}
