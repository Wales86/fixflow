<?php

namespace App\Http\Controllers;

use App\Dto\TimeTracking\CreateTimeEntryData;
use App\Dto\TimeTracking\UpdateTimeEntryData;
use App\Http\Requests\TimeEntry\TimeEntryRequest;
use App\Models\TimeEntry;
use App\Services\TimeEntryService;
use Illuminate\Http\RedirectResponse;

class TimeEntryController extends Controller
{
    public function __construct(
        protected TimeEntryService $timeEntryService,
    ) {}

    public function store(TimeEntryRequest $request): RedirectResponse
    {
        $this->timeEntryService->store(
            CreateTimeEntryData::from($request->validated())
        );

        return redirect()
            ->back()
            ->with('success', __('time_entry_added'));
    }

    public function update(TimeEntryRequest $request, TimeEntry $timeEntry): RedirectResponse
    {
        $this->timeEntryService->update(
            $timeEntry,
            UpdateTimeEntryData::from($request->validated())
        );

        return redirect()
            ->back()
            ->with('success', __('time_entry_updated'));
    }

    public function destroy(TimeEntry $timeEntry): RedirectResponse
    {
        $this->authorize('delete', $timeEntry);

        $this->timeEntryService->delete($timeEntry);

        return redirect()
            ->back()
            ->with('success', __('time_entry_deleted'));
    }
}
