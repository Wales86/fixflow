<?php

namespace App\Http\Controllers;

use App\Dto\RepairOrder\RepairOrderFiltersData;
use App\Dto\RepairOrder\RepairOrderIndexPagePropsData;
use App\Enums\RepairOrderStatus;
use App\Http\Requests\RepairOrders\CreateRepairOrderRequest;
use App\Http\Requests\RepairOrders\ListRepairOrdersRequest;
use App\Services\RepairOrderService;
use Inertia\Inertia;
use Inertia\Response;

class RepairOrderController extends Controller
{
    public function __construct(
        protected RepairOrderService $repairOrderService
    ) {}

    public function index(ListRepairOrdersRequest $request): Response
    {
        $repairOrdersPaginated = $this->repairOrderService->paginatedList($request->validated());

        $props = RepairOrderIndexPagePropsData::from([
            'tableData' => $repairOrdersPaginated,
            'filters' => RepairOrderFiltersData::from($request->validated()),
            'statusOptions' => RepairOrderStatus::options(),
        ]);

        return Inertia::render('repair-orders/index', $props);
    }

    public function create(CreateRepairOrderRequest $request): Response
    {
        $props = $this->repairOrderService->createFormData(
            preselectedVehicleId: $request->validated('preselected_vehicle_id')
        );

        return Inertia::render('repair-orders/create', $props);
    }
}
