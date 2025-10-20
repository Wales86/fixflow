<?php

namespace App\Services;

use App\Dto\InternalNote\StoreInternalNoteData;
use App\Models\InternalNote;
use App\Models\User;

class InternalNoteService
{
    public function store(StoreInternalNoteData $data, User $author): InternalNote
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
}
