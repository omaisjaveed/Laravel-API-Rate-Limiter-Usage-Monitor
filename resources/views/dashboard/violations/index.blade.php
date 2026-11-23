@extends('layouts.app')

@section('title', 'Violations')
@section('heading', 'Rate Limit Violations')
@section('subtitle', 'Blocked requests (HTTP 429) and violation statistics')

@section('content')
    <div class="mb-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Total violations</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $totalViolations }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Today</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $violationsToday }}</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Clients affected</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $affectedClients }}</div>
        </div>
    </div>

    @if ($topOffenders->isNotEmpty())
        <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900">Top clients by violations</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($topOffenders as $row)
                    <li class="flex items-center justify-between">
                        <span class="text-slate-700">{{ $row->client?->name ?? 'Unknown' }}</span>
                        <span class="font-medium text-red-700">{{ $row->violations_count }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('dashboard.violations.index') }}" class="mb-4 flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center">
        <select name="client" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All clients</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((string) $clientId === (string) $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ $from }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ $to }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
        <a href="{{ route('dashboard.violations.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Request</th>
                        <th class="px-4 py-3">Rate limit</th>
                        <th class="px-4 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($violations as $log)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $log->requested_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $log->client?->name ?? '—' }}</div>
                                <div class="font-mono text-xs text-slate-500">{{ $log->apiKey?->key_prefix ? $log->apiKey->key_prefix.'…' : '—' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold">{{ $log->method }}</span>
                                <span class="text-slate-600">{{ $log->endpoint }}</span>
                                <div class="text-xs text-red-600">HTTP {{ $log->status_code }} blocked</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if ($log->rate_limit_limit !== null)
                                    {{ $log->rate_limit_remaining ?? 0 }} / {{ $log->rate_limit_limit }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No rate-limit violations recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($violations->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $violations->links() }}
            </div>
        @endif
    </div>
@endsection
