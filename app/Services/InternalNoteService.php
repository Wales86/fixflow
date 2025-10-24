<?php

namespace App\Services;

use App\Dto\InternalNote\StoreInternalNoteData;
use App\Dto\InternalNote\UpdateInternalNoteData;
use App\Models\InternalNote;
use App\Models\Mechanic;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class InternalNoteService
{
    public function store(StoreInternalNoteData $data, User|Mechanic $author): InternalNote
    {
        $internalNote = InternalNote::create([
            'notable_type' => $data->notable_type,
            'notable_id' => $data->notable_id,
            'content' => $data->content,
            'author_id' => $author->id,
            'author_type' => get_class($author),
        ]);

        return $internalNote->fresh();
    }

    public function update(InternalNote $internalNote, UpdateInternalNoteData $data): InternalNote
    {
        $internalNote->update($data->all());

        return $internalNote->fresh();
    }

    public function delete(InternalNote $internalNote): void
    {
        try {
            $internalNote->delete();
        } catch (\Exception $e) {
            Log::error('Error deleting internal note: '.$e->getMessage());

            throw $e;
        }
    }
}
