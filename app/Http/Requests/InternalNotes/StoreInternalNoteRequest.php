<?php

namespace App\Http\Requests\InternalNotes;

use App\Enums\NotableType;
use App\Models\InternalNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInternalNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', InternalNote::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notable_type' => [
                'required',
                Rule::enum(NotableType::class),
            ],
            'notable_id' => ['required', 'integer'],
            'content' => ['required', 'string', 'max:5000'],
        ];
    }

    public function getNotableModelClass(): string
    {
        return NotableType::from($this->input('notable_type'))->modelClass();
    }
}
