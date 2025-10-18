<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Dto\Vehicle\StoreVehicleData;
use App\Http\Requests\Vehicle\VehicleIndexRequest;
use App\Http\Requests\Vehicles\CreateVehicleRequest;
use App\Http\Requests\Vehicles\StoreVehicleRequest;
use App\Services\ClientService;
use App\Services\VehicleService;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicleData = StoreVehicleData::from($request->validated());

        $this->vehicleService->store($vehicleData);

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Pojazd został dodany');
    }
}
