@extends('layouts.app')

@section('title', 'Request Logs')
@section('heading', 'API Request Logs')
@section('subtitle', 'Usage history without secrets — no raw keys or auth headers stored')

@section('content')
    <form method="GET" action="{{ route('dashboard.logs.index') }}" class="mb-4 grid gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
        <select name="client" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All clients</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" @selected((string) $filters['client'] === (string) $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>

        <input type="text" name="endpoint" value="{{ $filters['endpoint'] }}" placeholder="Endpoint contains..."
               class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <select name="method" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">All methods</option>
            @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                <option value="{{ $method }}" @selected($filters['method'] === $method)>{{ $method }}</option>
            @endforeach
        </select>

        <input type="number" name="status" value="{{ $filters['status'] }}" placeholder="Status code"
               class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <select name="blocked" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
            <option value="">Blocked: any</option>
            <option value="1" @selected($filters['blocked'] === '1')>Blocked only</option>
            <option value="0" @selected($filters['blocked'] === '0')>Not blocked</option>
        </select>

        <input type="date" name="from" value="{{ $filters['from'] }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ $filters['to'] }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">

        <div class="flex gap-2">
            <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
            <a href="{{ route('dashboard.logs.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Request</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Rate limit</th>
                        <th class="px-4 py-3">Time (ms)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">
                                {{ $log->requested_at?->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $log->client?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $log->apiKey?->key_prefix ? $log->apiKey->key_prefix.'…' : '—' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-800">{{ $log->method }}</span>
                                <span class="text-slate-600">{{ $log->endpoint }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ $log->was_blocked ? 'bg-red-50 text-red-700' : ($log->status_code >= 400 ? 'bg-amber-50 text-amber-700' : 'bg-teal-50 text-teal-700') }}">
                                    {{ $log->status_code }}{{ $log->was_blocked ? ' blocked' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                @if ($log->rate_limit_limit !== null)
                                    {{ $log->rate_limit_remaining ?? 0 }} / {{ $log->rate_limit_limit }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->response_time_ms }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                No request logs yet. Call an API endpoint with your key first.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
