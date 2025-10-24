<?php

namespace App\Http\Controllers;

use App\Dto\Common\FilterableTablePagePropsData;
use App\Dto\Common\FiltersData;
use App\Dto\Mechanic\GetMechanicsData;
use App\Dto\Mechanic\MechanicData;
use App\Dto\Mechanic\StoreMechanicData;
use App\Dto\Mechanic\UpdateMechanicData;
use App\Http\Requests\Mechanic\MechanicIndexRequest;
use App\Http\Requests\Mechanic\StoreMechanicRequest;
use App\Http\Requests\Mechanic\UpdateMechanicRequest;
use App\Models\Mechanic;
use App\Services\MechanicService;
use Illuminate\Http\RedirectResponse;
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

    public function create(): Response
    {
        $this->authorize('create', Mechanic::class);

        return Inertia::render('mechanics/create');
    }

    public function store(StoreMechanicRequest $request): RedirectResponse
    {
        $mechanicData = StoreMechanicData::from($request->validated());

        $this->mechanicService->create($mechanicData);

        return redirect()
            ->route('mechanics.index')
            ->with('success', __('mechanics.messages.created'));
    }

    public function edit(Mechanic $mechanic): Response
    {
        $this->authorize('update', $mechanic);

        return Inertia::render('mechanics/edit', [
            'mechanic' => MechanicData::from($mechanic),
        ]);
    }

    public function update(UpdateMechanicRequest $request, Mechanic $mechanic): RedirectResponse
    {
        $mechanicData = UpdateMechanicData::from($request->validated());

        $this->mechanicService->update($mechanic, $mechanicData);

        return redirect()
            ->route('mechanics.index')
            ->with('success', __('mechanics.messages.updated'));
    }
}
