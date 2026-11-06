<?php

namespace App\Services;

use App\Models\ApiClient;
use App\Models\ApiRequestLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardMetrics
{
    /**
     * @return array{
     *     total_requests: int,
     *     successful_requests: int,
     *     failed_requests: int,
     *     rate_limit_violations: int,
     *     active_clients: int,
     *     activity: array{labels: list<string>, values: list<int>},
     *     top_clients: Collection<int, object>,
     *     recent_activity: Collection<int, ApiRequestLog>
     * }
     */
    public function overview(): array
    {
        $total = ApiRequestLog::query()->count();
        $successful = ApiRequestLog::query()->whereBetween('status_code', [200, 299])->count();
        $violations = ApiRequestLog::query()->where('was_blocked', true)->count();
        $failed = ApiRequestLog::query()
            ->where('status_code', '>=', 400)
            ->where('was_blocked', false)
            ->count();

        return [
            'total_requests' => $total,
            'successful_requests' => $successful,
            'failed_requests' => $failed,
            'rate_limit_violations' => $violations,
            'active_clients' => ApiClient::query()->where('is_active', true)->count(),
            'activity' => $this->activityLastDays(7),
            'top_clients' => $this->topClients(5),
            'recent_activity' => ApiRequestLog::query()
                ->with(['client:id,name', 'apiKey:id,key_prefix'])
                ->latest('requested_at')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    private function activityLastDays(int $days): array
    {
        $start = Carbon::today()->subDays($days - 1)->startOfDay();

        $rows = ApiRequestLog::query()
            ->selectRaw('DATE(requested_at) as day, COUNT(*) as total')
            ->where('requested_at', '>=', $start)
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('M j');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * @return Collection<int, object>
     */
    private function topClients(int $limit): Collection
    {
        return ApiRequestLog::query()
            ->select('api_client_id', DB::raw('COUNT(*) as requests_count'))
            ->whereNotNull('api_client_id')
            ->groupBy('api_client_id')
            ->orderByDesc('requests_count')
            ->limit($limit)
            ->with('client:id,name,is_active')
            ->get();
    }
}
