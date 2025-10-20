<?php

namespace App\Http\Controllers;

use App\Dto\InternalNote\StoreInternalNoteData;
use App\Http\Requests\InternalNotes\StoreInternalNoteRequest;
use App\Services\InternalNoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class InternalNoteController extends Controller
{
    public function __construct(
        protected InternalNoteService $internalNoteService
    ) {}

    public function store(StoreInternalNoteRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $notable = $validated['notable_type']::withoutGlobalScopes()->findOrFail($validated['notable_id']);

        Gate::authorize('view', $notable);

        $storeData = StoreInternalNoteData::from($validated);

        $this->internalNoteService->store($storeData, $request->user());

        return back()->with('success', __('internal_notes.messages.created'));
    }
}
