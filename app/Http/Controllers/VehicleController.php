<?php

namespace App\Http\Controllers;

use App\Dto\Common\FiltersData;
use App\Dto\Vehicle\VehicleIndexPagePropsData;
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
        $vehicles = $this->vehicleService->list($request->validated());

        $props = VehicleIndexPagePropsData::from([
            'vehicles' => $vehicles,
            'filters' => FiltersData::from($request->validated()),
        ]);

        return Inertia::render('vehicles/index', $props);
    }
}
