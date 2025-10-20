<?php

namespace App\Dto\InternalNote;

use App\Models\InternalNote;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class InternalNoteData extends Data
{
    public function __construct(
        public int $id,
        public string $notable_type,
        public int $notable_id,
        public string $content,
        public int $author_id,
        public string $author_type,
        public string $created_at,
        public ?InternalNoteAuthorData $author = null,
    ) {}

    public static function fromInternalNote(InternalNote $note): self
    {
        return new self(
            id: $note->id,
            notable_type: $note->notable_type,
            notable_id: $note->notable_id,
            content: $note->content,
            author_id: $note->author_id,
            author_type: $note->author_type,
            created_at: $note->created_at->format('Y-m-d H:i:s'),
            author: $note->author ? InternalNoteAuthorData::from([
                'id' => $note->author->id,
                'name' => $note->author->name,
                'type' => class_basename($note->author_type),
            ]) : null,
        );
    }
}
