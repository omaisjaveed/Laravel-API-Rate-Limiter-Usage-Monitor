@extends('layouts.app')

@section('title', 'Overview')
@section('heading', 'Dashboard Overview')
@section('subtitle', 'High-level API usage and rate-limit health')

@section('content')
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Total requests</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($total_requests) }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Successful</div>
            <div class="mt-1 text-2xl font-semibold text-teal-700">{{ number_format($successful_requests) }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Failed</div>
            <div class="mt-1 text-2xl font-semibold text-amber-700">{{ number_format($failed_requests) }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Violations</div>
            <div class="mt-1 text-2xl font-semibold text-red-700">{{ number_format($rate_limit_violations) }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Active clients</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($active_clients) }}</div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900">Request activity (last 7 days)</h2>
                <a href="{{ route('dashboard.logs.index') }}" class="text-xs font-medium text-teal-700 hover:underline">View logs</a>
            </div>
            <div class="mt-4 h-64">
                <canvas id="activityChart" aria-label="Request activity chart"></canvas>
            </div>
            @if (array_sum($activity['values']) === 0)
                <p class="mt-2 text-sm text-slate-500">No requests in the last 7 days yet.</p>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-sm font-semibold text-slate-900">Top API clients</h2>
                <a href="{{ route('dashboard.clients.index') }}" class="text-xs font-medium text-teal-700 hover:underline">Manage</a>
            </div>
            <ul class="mt-4 space-y-3">
                @forelse ($top_clients as $row)
                    <li class="flex items-center justify-between gap-3 text-sm">
                        <div>
                            <div class="font-medium text-slate-800">{{ $row->client?->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $row->client?->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                        <div class="font-semibold text-slate-900">{{ number_format($row->requests_count) }}</div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No client traffic yet.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mt-4 rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-900">Recent activity</h2>
            <a href="{{ route('dashboard.violations.index') }}" class="text-xs font-medium text-teal-700 hover:underline">View violations</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Time</th>
                        <th class="px-5 py-3">Client</th>
                        <th class="px-5 py-3">Request</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">ms</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recent_activity as $log)
                        <tr>
                            <td class="px-5 py-3 whitespace-nowrap text-slate-600">{{ $log->requested_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-5 py-3 text-slate-800">{{ $log->client?->name ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="font-semibold">{{ $log->method }}</span>
                                <span class="text-slate-600">{{ $log->endpoint }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ $log->was_blocked ? 'bg-red-50 text-red-700' : ($log->status_code >= 400 ? 'bg-amber-50 text-amber-700' : 'bg-teal-50 text-teal-700') }}">
                                    {{ $log->status_code }}{{ $log->was_blocked ? ' blocked' : '' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $log->response_time_ms }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                No recent API activity. Call <code class="text-xs">/api/v1/products</code> with your API key.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const ctx = document.getElementById('activityChart');
            if (!ctx || typeof Chart === 'undefined') return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($activity['labels']),
                    datasets: [{
                        label: 'Requests',
                        data: @json($activity['values']),
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(13, 148, 136, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#0f766e',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        })();
    </script>
@endsection
