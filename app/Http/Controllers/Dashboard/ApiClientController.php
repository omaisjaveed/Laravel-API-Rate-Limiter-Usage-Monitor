<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiClient\StoreApiClientRequest;
use App\Http\Requests\ApiClient\UpdateApiClientRequest;
use App\Models\ApiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiClientController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ApiClient::class);

        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $clients = ApiClient::query()
            ->withCount(['apiKeys', 'activeApiKeys'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.clients.index', compact('clients', 'search', 'status'));
    }

    public function create(): View
    {
        $this->authorize('create', ApiClient::class);

        return view('dashboard.clients.create');
    }

    public function store(StoreApiClientRequest $request): RedirectResponse
    {
        $this->authorize('create', ApiClient::class);

        $client = ApiClient::query()->create($request->validated());

        return redirect()
            ->route('dashboard.clients.index')
            ->with('status', "API client \"{$client->name}\" created successfully.");
    }

    public function edit(ApiClient $client): View
    {
        $this->authorize('update', $client);

        return view('dashboard.clients.edit', compact('client'));
    }

    public function update(UpdateApiClientRequest $request, ApiClient $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()
            ->route('dashboard.clients.index')
            ->with('status', "API client \"{$client->name}\" updated successfully.");
    }

    public function toggle(ApiClient $client): RedirectResponse
    {
        $this->authorize('toggle', $client);

        $client->update([
            'is_active' => ! $client->is_active,
        ]);

        $state = $client->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('dashboard.clients.index')
            ->with('status', "API client \"{$client->name}\" {$state}.");
    }
}
