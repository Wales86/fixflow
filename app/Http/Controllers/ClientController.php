<?php

namespace App\Http\Controllers;

use App\Dto\Client\ClientData;
use App\Dto\Client\ClientIndexPagePropsData;
use App\Dto\Client\StoreClientData;
use App\Dto\Common\FiltersData;
use App\Http\Requests\Client\IndexClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    public function index(IndexClientRequest $request): Response
    {
        $clients = $this->clientService->list($request->validated());

        $props = ClientIndexPagePropsData::from([
            'clients' => $clients,
            'filters' => FiltersData::from($request->validated()),
        ]);

        return Inertia::render('clients/index', $props);
    }

    public function create(): Response
    {
        $this->authorize('create', Client::class);

        return Inertia::render('clients/create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $clientData = StoreClientData::from($request->validated());

        $client = $this->clientService->create($clientData);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Klient został dodany');
    }

    public function edit(Client $client): Response
    {
        $this->authorize('update', $client);

        $clientData = ClientData::from($client);

        return Inertia::render('clients/edit', [
            'client' => $clientData,
        ]);
    }
}
