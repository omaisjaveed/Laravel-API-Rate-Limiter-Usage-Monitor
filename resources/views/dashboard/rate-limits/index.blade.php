@extends('layouts.app')

@section('title', 'Rate Limits')
@section('heading', 'Rate Limit Settings')
@section('subtitle', 'Per-client minute, hour, and day limits with live usage')

@section('content')
    <div class="space-y-4">
        @forelse ($clients as $row)
            @php
                /** @var \App\Models\ApiClient $client */
                $client = $row['model'];
                $usage = $row['usage'];
            @endphp

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $client->name }}</h2>
                        <p class="text-sm text-slate-500">
                            {{ $client->is_active ? 'Active client' : 'Inactive client' }}
                        </p>
                    </div>
                    <a href="{{ route('dashboard.clients.edit', $client) }}" class="text-sm font-medium text-teal-700 hover:underline">
                        Edit client
                    </a>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    @foreach (['minute' => 'Per minute', 'hour' => 'Per hour', 'day' => 'Per day'] as $key => $label)
                        @php $window = $usage[$key]; @endphp
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <div class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $label }}</div>
                            <div class="mt-1 text-sm text-slate-800">
                                <span class="text-xl font-semibold">{{ $window['used'] }}</span>
                                <span class="text-slate-500">/ {{ $window['limit'] }} used</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                Remaining: {{ $window['remaining'] }}
                                · resets {{ \Illuminate\Support\Carbon::createFromTimestamp($window['reset_at'])->diffForHumans() }}
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                @php
                                    $pct = $window['limit'] > 0 ? min(100, (int) round(($window['used'] / $window['limit']) * 100)) : 0;
                                @endphp
                                <div class="h-full {{ $pct >= 100 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber-500' : 'bg-teal-600') }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('dashboard.rate-limits.update', $client) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4 sm:grid-cols-4 sm:items-end">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-slate-600">Minute limit</label>
                        <input type="number" min="1" name="rate_limit_per_minute" required
                               value="{{ old('rate_limit_per_minute', $client->rate_limit_per_minute) }}"
                               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Hour limit</label>
                        <input type="number" min="1" name="rate_limit_per_hour" required
                               value="{{ old('rate_limit_per_hour', $client->rate_limit_per_hour) }}"
                               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600">Day limit</label>
                        <input type="number" min="1" name="rate_limit_per_day" required
                               value="{{ old('rate_limit_per_day', $client->rate_limit_per_day) }}"
                               class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                        Save limits
                    </button>
                </form>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                No API clients yet.
                <a href="{{ route('dashboard.clients.create') }}" class="text-teal-700 hover:underline">Create one</a>
                to configure rate limits.
            </div>
        @endforelse
    </div>
@endsection
