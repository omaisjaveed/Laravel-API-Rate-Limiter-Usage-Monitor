<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiClient\UpdateRateLimitsRequest;
use App\Models\ApiClient;
use App\Services\ApiRateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RateLimitController extends Controller
{
    public function __construct(
        private readonly ApiRateLimiter $rateLimiter,
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', ApiClient::class);

        $clients = ApiClient::query()
            ->orderBy('name')
            ->get()
            ->map(function (ApiClient $client) {
                $usage = $this->rateLimiter->currentUsage($client);

                return [
                    'model' => $client,
                    'usage' => $usage,
                ];
            });

        return view('dashboard.rate-limits.index', compact('clients'));
    }

    public function update(UpdateRateLimitsRequest $request, ApiClient $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()
            ->route('dashboard.rate-limits.index')
            ->with('status', "Rate limits updated for \"{$client->name}\".");
    }
}
