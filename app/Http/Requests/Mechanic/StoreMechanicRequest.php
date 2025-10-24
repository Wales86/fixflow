<?php

namespace App\Http\Requests\Mechanic;

use App\Models\Mechanic;
use Illuminate\Foundation\Http\FormRequest;

class StoreMechanicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Mechanic::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
