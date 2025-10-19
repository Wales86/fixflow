<?php

namespace App\Http\Controllers;

use App\Dto\RepairOrder\RepairOrderFiltersData;
use App\Dto\RepairOrder\RepairOrderIndexPagePropsData;
use App\Dto\RepairOrder\StoreRepairOrderData;
use App\Enums\RepairOrderStatus;
use App\Http\Requests\RepairOrders\CreateRepairOrderRequest;
use App\Http\Requests\RepairOrders\ListRepairOrdersRequest;
use App\Http\Requests\RepairOrders\StoreRepairOrderRequest;
use App\Models\RepairOrder;
use App\Services\RepairOrderService;
use Illuminate\Http\RedirectResponse;
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

    public function store(StoreRepairOrderRequest $request): RedirectResponse
    {
        $repairOrderData = StoreRepairOrderData::from($request->validated());

        $this->repairOrderService->store($repairOrderData);

        return redirect()
            ->route('repair-orders.index')
            ->with('success', __('repair_orders.messages.created'));
    }

    public function edit(RepairOrder $repairOrder): Response
    {
        $this->authorize('update', $repairOrder);

        $props = $this->repairOrderService->prepareDataForEditPage($repairOrder);

        return Inertia::render('repair-orders/edit', $props);
    }
}
