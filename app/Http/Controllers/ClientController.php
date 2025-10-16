<?php

namespace App\Http\Controllers;

use App\Dto\Client\ClientIndexPagePropsData;
use App\Dto\Common\FiltersData;
use App\Http\Requests\Client\IndexClientRequest;
use App\Models\Client;
use App\Services\ClientService;
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
}
