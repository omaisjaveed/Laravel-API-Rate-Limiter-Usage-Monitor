<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ViolationController extends Controller
{
    public function index(Request $request): View
    {
        $clientId = $request->query('client');
        $from = $request->query('from');
        $to = $request->query('to');

        $base = ApiRequestLog::query()->where('was_blocked', true);

        $totalViolations = (clone $base)->count();
        $violationsToday = (clone $base)->whereDate('requested_at', today())->count();
        $affectedClients = (clone $base)
            ->whereNotNull('api_client_id')
            ->distinct()
            ->count('api_client_id');

        $topOffenders = ApiRequestLog::query()
            ->select('api_client_id', DB::raw('COUNT(*) as violations_count'))
            ->where('was_blocked', true)
            ->whereNotNull('api_client_id')
            ->groupBy('api_client_id')
            ->orderByDesc('violations_count')
            ->limit(5)
            ->with('client:id,name')
            ->get();

        $violations = ApiRequestLog::query()
            ->with(['client:id,name', 'apiKey:id,key_prefix,name'])
            ->where('was_blocked', true)
            ->when($clientId, fn ($q) => $q->where('api_client_id', $clientId))
            ->when($from, fn ($q) => $q->whereDate('requested_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('requested_at', '<=', $to))
            ->latest('requested_at')
            ->paginate(20)
            ->withQueryString();

        $clients = ApiClient::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.violations.index', compact(
            'violations',
            'clients',
            'clientId',
            'from',
            'to',
            'totalViolations',
            'violationsToday',
            'affectedClients',
            'topOffenders',
        ));
    }
}
