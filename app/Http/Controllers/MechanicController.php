<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Dto\Mechanic\GetMechanicsData;
use App\Http\Requests\Mechanic\MechanicIndexRequest;
use App\Services\MechanicService;
use Inertia\Inertia;
use Inertia\Response;

class MechanicController extends Controller
{
    public function __construct(
        protected MechanicService $mechanicService
    ) {}

    public function index(MechanicIndexRequest $request): Response
    {
        $getMechanicsData = GetMechanicsData::from($request->validated());

        $mechanicsPaginated = $this->mechanicService->getMechanics($getMechanicsData);

        $props = FilterableTablePagePropsData::from([
            'tableData' => $mechanicsPaginated,
            'filters' => FiltersData::from([]),
        ]);

        return Inertia::render('mechanics/index', $props);
    }
}
