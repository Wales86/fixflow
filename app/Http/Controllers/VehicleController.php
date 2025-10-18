<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Http\Requests\Vehicle\VehicleIndexRequest;
use App\Http\Requests\Vehicles\CreateVehicleRequest;
use App\Services\ClientService;
use App\Services\VehicleService;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function __construct(
        protected VehicleService $vehicleService
    ) {}

    public function index(VehicleIndexRequest $request): Response
    {
        $vehiclesPaginated = $this->vehicleService->paginatedList($request->validated());

        $props = FilterableTablePagePropsData::from([
            'tableData' => $vehiclesPaginated,
            'filters' => FiltersData::from($request->validated()),
        ]);

        return Inertia::render('vehicles/index', $props);
    }

    public function create(CreateVehicleRequest $request, ClientService $clientService): Response
    {
        $validated = $request->validated();

        return Inertia::render('vehicles/create', [
            'clients' => $clientService->getClientsForSelect(),
            'preselectedClientId' => isset($validated['preselected_client_id'])
                ? (int) $validated['preselected_client_id']
                : null,
        ]);
    }
}
