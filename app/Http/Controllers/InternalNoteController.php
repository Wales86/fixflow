<?php

namespace App\Http\Controllers;

use App\Dto\InternalNote\StoreInternalNoteData;
use App\Dto\InternalNote\UpdateInternalNoteData;
use App\Http\Requests\InternalNotes\StoreInternalNoteRequest;
use App\Http\Requests\InternalNotes\UpdateInternalNoteRequest;
use App\Models\InternalNote;
use App\Models\Mechanic;
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
            'mechanic_id' => $validated['mechanic_id'] ?? null,
        ]);

        // If mechanic_id is provided, use Mechanic as author, otherwise use User
        $author = isset($validated['mechanic_id'])
            ? Mechanic::findOrFail($validated['mechanic_id'])
            : $request->user();

        $this->internalNoteService->store($storeData, $author);

        return back()->with('success', __('internal_notes.messages.created'));
    }

    public function update(UpdateInternalNoteRequest $request, InternalNote $internalNote): RedirectResponse
    {
        $updateInternalNoteData = UpdateInternalNoteData::from($request->validated());

        $this->internalNoteService->update($internalNote, $updateInternalNoteData);

        return back()->with('success', __('internal_notes.messages.updated'));
    }

    public function destroy(InternalNote $internalNote): RedirectResponse
    {
        $this->authorize('delete', $internalNote);

        try {
            $this->internalNoteService->delete($internalNote);
        } catch (\Exception) {
            return back()->with('error', 'Nie udało się usunąć notatki');
        }

        return back()->with('success', __('internal_notes.messages.deleted'));
    }
}
