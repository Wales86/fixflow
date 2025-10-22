<?php

namespace App\Http\Controllers;

use App\Dto\InternalNote\StoreInternalNoteData;
use App\Dto\InternalNote\UpdateInternalNoteData;
use App\Http\Requests\InternalNotes\StoreInternalNoteRequest;
use App\Http\Requests\InternalNotes\UpdateInternalNoteRequest;
use App\Models\InternalNote;
use App\Services\InternalNoteService;
use Illuminate\Http\RedirectResponse;

class InternalNoteController extends Controller
{
    public function __construct(
        protected InternalNoteService $internalNoteService
    ) {}

    public function store(StoreInternalNoteRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $modelClass = $request->getNotableModelClass();

        $modelClass::findOrFail($validated['notable_id']);

        $storeData = StoreInternalNoteData::from([
            'notable_type' => $modelClass,
            'notable_id' => $validated['notable_id'],
            'content' => $validated['content'],
        ]);

        $this->internalNoteService->store($storeData, $request->user());

        return back()->with('success', __('internal_notes.messages.created'));
    }

    public function update(UpdateInternalNoteRequest $request, InternalNote $internalNote): RedirectResponse
    {
        $updateInternalNoteData = UpdateInternalNoteData::from($request->validated());

        $this->internalNoteService->update($internalNote, $updateInternalNoteData);

        return back()->with('success', __('internal_notes.messages.updated'));
    }
}
