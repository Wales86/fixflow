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
        $workshopId = $this->user()->workshop_id;

        $mechanicRule = $this->user()->hasRole('Mechanic')
            ? 'required'
            : 'nullable';

        return [
            'notable_type' => [
                'required',
                Rule::enum(NotableType::class),
            ],
            'notable_id' => ['required', 'integer'],
            'content' => ['required', 'string', 'max:5000'],
            'mechanic_id' => [
                $mechanicRule,
                'integer',
                "exists:mechanics,id,workshop_id,{$workshopId},is_active,1",
            ],
        ];
    }

    public function getNotableModelClass(): string
    {
        return NotableType::from($this->input('notable_type'))->modelClass();
    }
}
