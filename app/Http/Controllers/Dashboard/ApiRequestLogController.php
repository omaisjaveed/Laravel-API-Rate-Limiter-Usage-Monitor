<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiRequestLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'client' => $request->query('client'),
            'endpoint' => trim((string) $request->query('endpoint', '')),
            'method' => strtoupper((string) $request->query('method', '')),
            'status' => $request->query('status'),
            'blocked' => $request->query('blocked'),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ];

        $logs = ApiRequestLog::query()
            ->with(['client:id,name', 'apiKey:id,key_prefix,name'])
            ->when($filters['client'], fn ($q) => $q->where('api_client_id', $filters['client']))
            ->when($filters['endpoint'] !== '', fn ($q) => $q->where('endpoint', 'like', '%'.$filters['endpoint'].'%'))
            ->when($filters['method'] !== '', fn ($q) => $q->where('method', $filters['method']))
            ->when($filters['status'] !== null && $filters['status'] !== '', fn ($q) => $q->where('status_code', (int) $filters['status']))
            ->when($filters['blocked'] === '1', fn ($q) => $q->where('was_blocked', true))
            ->when($filters['blocked'] === '0', fn ($q) => $q->where('was_blocked', false))
            ->when($filters['from'], fn ($q) => $q->whereDate('requested_at', '>=', $filters['from']))
            ->when($filters['to'], fn ($q) => $q->whereDate('requested_at', '<=', $filters['to']))
            ->latest('requested_at')
            ->paginate(20)
            ->withQueryString();

        $clients = ApiClient::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.logs.index', compact('logs', 'clients', 'filters'));
    }
}
