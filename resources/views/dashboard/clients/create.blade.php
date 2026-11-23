@extends('layouts.app')

@section('title', 'New API Client')
@section('heading', 'Create API Client')
@section('subtitle', 'Set identity, status, and independent rate limits')

@section('content')
    <div class="mx-auto max-w-3xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('dashboard.clients.store') }}" class="space-y-6">
            @csrf
            @include('dashboard.clients._form')

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">
                    Create client
                </button>
                <a href="{{ route('dashboard.clients.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Cancel</a>
            </div>
        </form>
    </div>
@endsection
