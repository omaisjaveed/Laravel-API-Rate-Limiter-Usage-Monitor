@extends('layouts.app')

@section('title', 'API Clients')
@section('heading', 'API Clients')
@section('subtitle', 'Create, edit, activate, and deactivate API clients')

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('dashboard.clients.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search clients..."
                   class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
            <select name="status" class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600">
                <option value="">All statuses</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="inactive" @selected($status === 'inactive')>Inactive</option>
            </select>
            <button type="submit" class="rounded-md bg-slate-800 px-3 py-2 text-sm font-medium text-white hover:bg-slate-900">Filter</button>
        </form>

        <a href="{{ route('dashboard.clients.create') }}"
           class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
            New client
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Limits (m / h / d)</th>
                        <th class="px-4 py-3">Keys</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $client->name }}</div>
                                @if ($client->description)
                                    <div class="mt-0.5 text-xs text-slate-500 line-clamp-1">{{ $client->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($client->is_active)
                                    <span class="inline-flex rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700">Active</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $client->rate_limit_per_minute }} / {{ $client->rate_limit_per_hour }} / {{ $client->rate_limit_per_day }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $client->active_api_keys_count }} active
                                <span class="text-slate-400">({{ $client->api_keys_count }} total)</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('dashboard.clients.edit', $client) }}"
                                       class="rounded-md border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.clients.toggle', $client) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="rounded-md border px-2.5 py-1.5 text-xs font-medium {{ $client->is_active ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-teal-300 text-teal-700 hover:bg-teal-50' }}">
                                            {{ $client->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                No API clients yet.
                                <a href="{{ route('dashboard.clients.create') }}" class="text-teal-700 hover:underline">Create the first one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
@endsection
