<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use App\Services\ApiRateLimiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceApiRateLimit
{
    public function __construct(
        private readonly ApiRateLimiter $rateLimiter,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        /** @var ApiClient|null $client */
        $client = $request->attributes->get('api_client');

        if (! $client instanceof ApiClient) {
            return response()->json([
                'message' => 'API client context missing.',
                'error' => 'unauthenticated',
            ], 401);
        }

        $result = $this->rateLimiter->attempt($client);

        $request->attributes->set('rate_limit', $result);

        if (! $result['allowed']) {
            return response()->json([
                'message' => 'Too many requests. Rate limit exceeded.',
                'error' => 'rate_limit_exceeded',
                'window' => $result['window'],
            ], 429, $this->headers($result, blocked: true));
        }

        $response = $next($request);

        foreach ($this->headers($result, blocked: false) as $header => $value) {
            $response->headers->set($header, (string) $value);
        }

        return $response;
    }

    /**
     * @param  array{limit: int, remaining: int, reset_at: int, retry_after: int|null, window: string}  $result
     * @return array<string, int|string>
     */
    private function headers(array $result, bool $blocked): array
    {
        $headers = [
            'X-RateLimit-Limit' => $result['limit'],
            'X-RateLimit-Remaining' => $blocked ? 0 : $result['remaining'],
            'X-RateLimit-Reset' => $result['reset_at'],
            'X-RateLimit-Window' => $result['window'],
        ];

        if ($blocked && $result['retry_after'] !== null) {
            $headers['Retry-After'] = $result['retry_after'];
        }

        return $headers;
    }
}
