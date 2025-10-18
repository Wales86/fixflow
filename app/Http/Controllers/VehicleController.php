<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Http\Requests\Vehicle\VehicleIndexRequest;
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
}
