@extends('layouts.app')

@section('title', 'Edit API Client')
@section('heading', 'Edit API Client')
@section('subtitle', 'Update “{{ $client->name }}” settings and rate limits')

@section('content')
    <div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('dashboard.clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('dashboard.clients._form', ['client' => $client])

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                    Save changes
                </button>
                <a href="{{ route('dashboard.clients.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
@endsection
