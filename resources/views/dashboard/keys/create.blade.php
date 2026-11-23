@extends('layouts.app')

@section('title', 'Generate API Key')
@section('heading', 'Generate API Key')
@section('subtitle', 'The raw key is shown only once after creation')

@section('content')
    <div class="mx-auto max-w-2xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @if ($clients->isEmpty())
            <p class="text-sm text-slate-600">
                No active API clients found.
                <a href="{{ route('dashboard.clients.create') }}" class="font-medium text-teal-700 hover:underline">Create a client</a> first.
            </p>
        @else
            <form method="POST" action="{{ route('dashboard.keys.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="api_client_id" class="block text-sm font-medium text-slate-700">API client</label>
                    <select id="api_client_id" name="api_client_id" required
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('api_client_id') border-red-400 @enderror">
                        <option value="">Select client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected((string) old('api_client_id', $selectedClientId) === (string) $client->id)>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('api_client_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Key label (optional)</label>
                    <input id="name" name="name" type="text" maxlength="255" value="{{ old('name') }}"
                           placeholder="e.g. Production mobile app"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    Only a hashed key is stored. The full secret appears once after generate/regenerate.
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                        Generate key
                    </button>
                    <a href="{{ route('dashboard.keys.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
                </div>
            </form>
        @endif
    </div>
@endsection
