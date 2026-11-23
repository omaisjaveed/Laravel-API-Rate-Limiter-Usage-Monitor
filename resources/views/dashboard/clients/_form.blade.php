@php
    /** @var \App\Models\ApiClient|null $client */
    $client = $client ?? null;
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="block text-sm font-medium text-slate-700">Client name</label>
        <input id="name" name="name" type="text" required maxlength="255"
               value="{{ old('name', $client->name ?? '') }}"
               class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('name') border-red-400 @enderror">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
        <textarea id="description" name="description" rows="3" maxlength="1000"
                  class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('description') border-red-400 @enderror">{{ old('description', $client->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label for="rate_limit_per_minute" class="block text-sm font-medium text-slate-700">Per minute</label>
            <input id="rate_limit_per_minute" name="rate_limit_per_minute" type="number" min="1" required
                   value="{{ old('rate_limit_per_minute', $client->rate_limit_per_minute ?? 60) }}"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('rate_limit_per_minute') border-red-400 @enderror">
            @error('rate_limit_per_minute')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="rate_limit_per_hour" class="block text-sm font-medium text-slate-700">Per hour</label>
            <input id="rate_limit_per_hour" name="rate_limit_per_hour" type="number" min="1" required
                   value="{{ old('rate_limit_per_hour', $client->rate_limit_per_hour ?? 1000) }}"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('rate_limit_per_hour') border-red-400 @enderror">
            @error('rate_limit_per_hour')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="rate_limit_per_day" class="block text-sm font-medium text-slate-700">Per day</label>
            <input id="rate_limit_per_day" name="rate_limit_per_day" type="number" min="1" required
                   value="{{ old('rate_limit_per_day', $client->rate_limit_per_day ?? 10000) }}"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-1 focus:ring-teal-600 @error('rate_limit_per_day') border-red-400 @enderror">
            @error('rate_limit_per_day')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-slate-300 text-teal-600 focus:ring-teal-600"
               @checked(old('is_active', $client->is_active ?? true))>
        Active client
    </label>
</div>
