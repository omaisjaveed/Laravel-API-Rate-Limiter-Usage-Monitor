<?php

namespace App\Services;

use App\Models\ApiClient;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ApiRateLimiter
{
    public function __construct(
        private readonly ?CacheRepository $cache = null,
    ) {}

    private function store(): CacheRepository
    {
        return $this->cache ?? Cache::store(config('cache.default', 'database'));
    }

    /**
     * Attempt to consume one request against the client's fixed windows.
     *
     * Uses a per-client lock so concurrent requests cannot bypass limits.
     *
     * @return array{
     *     allowed: bool,
     *     limit: int,
     *     remaining: int,
     *     reset_at: int,
     *     retry_after: int|null,
     *     window: string
     * }
     */
    public function attempt(ApiClient $client): array
    {
        $windows = $this->windowsFor($client);
        $store = $this->store();
        $lockKey = "api_rate_limit_lock:client:{$client->id}";

        return $store->lock($lockKey, 5)->block(5, function () use ($store, $client, $windows) {
            $snapshots = [];

            foreach ($windows as $name => $window) {
                $cacheKey = $this->cacheKey($client->id, $name, $window['bucket']);
                $count = (int) $store->get($cacheKey, 0);

                $snapshots[$name] = [
                    'cache_key' => $cacheKey,
                    'count' => $count,
                    'limit' => $window['limit'],
                    'reset_at' => $window['reset_at'],
                    'ttl' => $window['ttl'],
                    'window' => $name,
                ];

                if ($count >= $window['limit']) {
                    return [
                        'allowed' => false,
                        'limit' => $window['limit'],
                        'remaining' => 0,
                        'reset_at' => $window['reset_at'],
                        'retry_after' => max(1, $window['reset_at'] - time()),
                        'window' => $name,
                    ];
                }
            }

            foreach ($snapshots as $snapshot) {
                if (! $store->has($snapshot['cache_key'])) {
                    $store->put($snapshot['cache_key'], 0, $snapshot['ttl']);
                }

                $store->increment($snapshot['cache_key']);
            }

            $remainingSnapshots = [];
            foreach ($snapshots as $name => $snapshot) {
                $used = $snapshot['count'] + 1;
                $remainingSnapshots[] = [
                    'limit' => $snapshot['limit'],
                    'remaining' => max(0, $snapshot['limit'] - $used),
                    'reset_at' => $snapshot['reset_at'],
                    'window' => $name,
                ];
            }

            $tightest = collect($remainingSnapshots)->sortBy('remaining')->first();

            return [
                'allowed' => true,
                'limit' => $tightest['limit'],
                'remaining' => $tightest['remaining'],
                'reset_at' => $tightest['reset_at'],
                'retry_after' => null,
                'window' => $tightest['window'],
            ];
        });
    }

    /**
     * Read current fixed-window usage without consuming a request.
     *
     * @return array{
     *     minute: array{used: int, limit: int, remaining: int, reset_at: int},
     *     hour: array{used: int, limit: int, remaining: int, reset_at: int},
     *     day: array{used: int, limit: int, remaining: int, reset_at: int}
     * }
     */
    public function currentUsage(ApiClient $client): array
    {
        $store = $this->store();
        $usage = [];

        foreach ($this->windowsFor($client) as $name => $window) {
            $used = (int) $store->get($this->cacheKey($client->id, $name, $window['bucket']), 0);

            $usage[$name] = [
                'used' => $used,
                'limit' => $window['limit'],
                'remaining' => max(0, $window['limit'] - $used),
                'reset_at' => $window['reset_at'],
            ];
        }

        return $usage;
    }

    private function cacheKey(int $clientId, string $window, string $bucket): string
    {
        return "api_rate_limit:{$clientId}:{$window}:{$bucket}";
    }

    /**
     * @return array<string, array{limit: int, bucket: string, reset_at: int, ttl: int}>
     */
    private function windowsFor(ApiClient $client): array
    {
        $now = Carbon::now();

        $minuteReset = $now->copy()->startOfMinute()->addMinute();
        $hourReset = $now->copy()->startOfHour()->addHour();
        $dayReset = $now->copy()->startOfDay()->addDay();

        return [
            'minute' => [
                'limit' => $client->rate_limit_per_minute,
                'bucket' => $now->format('YmdHi'),
                'reset_at' => $minuteReset->getTimestamp(),
                'ttl' => max(1, $now->diffInSeconds($minuteReset) + 1),
            ],
            'hour' => [
                'limit' => $client->rate_limit_per_hour,
                'bucket' => $now->format('YmdH'),
                'reset_at' => $hourReset->getTimestamp(),
                'ttl' => max(1, $now->diffInSeconds($hourReset) + 1),
            ],
            'day' => [
                'limit' => $client->rate_limit_per_day,
                'bucket' => $now->format('Ymd'),
                'reset_at' => $dayReset->getTimestamp(),
                'ttl' => max(1, $now->diffInSeconds($dayReset) + 1),
            ],
        ];
    }
}
