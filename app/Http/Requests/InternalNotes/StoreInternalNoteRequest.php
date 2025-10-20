<?php

namespace App\Http\Requests\InternalNotes;

use App\Models\Client;
use App\Models\InternalNote;
use App\Models\RepairOrder;
use App\Models\Vehicle;
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
                'string',
                Rule::in([RepairOrder::class, Client::class, Vehicle::class]),
            ],
            'notable_id' => ['required', 'integer'],
            'content' => ['required', 'string', 'max:5000'],
        ];
    }
}
