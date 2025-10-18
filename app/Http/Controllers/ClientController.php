<?php

namespace App\Http\Controllers;

use App\Dto\Client\ClientData;
use App\Dto\Client\StoreClientData;
use App\Dto\Client\UpdateClientData;
use App\Dto\Common\FiltersData;
use App\Exceptions\CannotDeleteClientWithVehiclesException;
use App\Http\Requests\Client\IndexClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Dto\Common\FilterableTablePagePropsData;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    public function index(IndexClientRequest $request): Response
    {
        $clientsPaginated = $this->clientService->paginatedList($request->validated());

        $props = FilterableTablePagePropsData::from([
            'tableData' => $clientsPaginated,
            'filters' => FiltersData::from($request->validated()),
        ]);

        return Inertia::render('clients/index', $props);
    }

    public function show(Client $client): Response
    {
        $this->authorize('view', $client);

        $props = $this->clientService->prepareClientShowData($client);

        return Inertia::render('clients/show', $props);
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

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $updateClientData = UpdateClientData::from($request->validated());

        $this->clientService->update($client, $updateClientData);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Klient został zaktualizowany');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        try {
            $this->clientService->deleteClient($client);
        } catch (CannotDeleteClientWithVehiclesException) {
            return redirect()
                ->back()
                ->with('error', 'Nie można usunąć klienta z przypisanymi pojazdami');
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Klient został usunięty');
    }
}
