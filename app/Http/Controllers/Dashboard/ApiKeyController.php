<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiKey\StoreApiKeyRequest;
use App\Models\ApiClient;
use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ApiKey::class);

        $clientId = $request->query('client');
        $status = $request->query('status');

        $keys = ApiKey::query()
            ->with('client')
            ->when($clientId, fn ($query) => $query->where('api_client_id', $clientId))
            ->when($status === 'active', fn ($query) => $query->whereNull('revoked_at'))
            ->when($status === 'revoked', fn ($query) => $query->whereNotNull('revoked_at'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $clients = ApiClient::query()->orderBy('name')->get(['id', 'name', 'is_active']);

        return view('dashboard.keys.index', compact('keys', 'clients', 'clientId', 'status'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', ApiKey::class);

        $clients = ApiClient::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedClientId = $request->query('client');

        return view('dashboard.keys.create', compact('clients', 'selectedClientId'));
    }

    public function store(StoreApiKeyRequest $request): RedirectResponse
    {
        $this->authorize('create', ApiKey::class);

        $client = ApiClient::query()->findOrFail($request->integer('api_client_id'));
        $generated = ApiKey::generateFor($client, $request->string('name')->toString() ?: null);

        return redirect()
            ->route('dashboard.keys.index', ['client' => $client->id])
            ->with('status', 'API key generated. Copy it now — it will not be shown again.')
            ->with('plain_api_key', $generated['plain_text']);
    }

    public function revoke(ApiKey $key): RedirectResponse
    {
        $this->authorize('revoke', $key);

        if ($key->isRevoked()) {
            return redirect()
                ->route('dashboard.keys.index', ['client' => $key->api_client_id])
                ->with('status', 'This API key is already revoked.');
        }

        $key->revoke();

        return redirect()
            ->route('dashboard.keys.index', ['client' => $key->api_client_id])
            ->with('status', 'API key revoked successfully.');
    }

    public function regenerate(ApiKey $key): RedirectResponse
    {
        $this->authorize('regenerate', $key);

        $generated = $key->regenerate();

        return redirect()
            ->route('dashboard.keys.index', ['client' => $key->api_client_id])
            ->with('status', 'API key regenerated. Copy the new key now — it will not be shown again.')
            ->with('plain_api_key', $generated['plain_text']);
    }
}
