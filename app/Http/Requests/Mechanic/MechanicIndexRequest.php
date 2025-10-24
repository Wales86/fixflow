<?php

namespace App\Http\Requests\Mechanic;

use App\Models\Mechanic;
use Illuminate\Foundation\Http\FormRequest;

class MechanicIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Mechanic::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'active' => ['nullable', 'boolean'],
        ];
    }
}
