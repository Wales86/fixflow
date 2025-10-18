<?php

namespace App\Http\Requests\Vehicles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('vehicle'));
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workshopId = $this->user()->workshop_id;
        $vehicle = $this->route('vehicle');

        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where('workshop_id', $workshopId),
            ],
            'make' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'vin' => [
                'required',
                'string',
                'max:17',
                Rule::unique('vehicles', 'vin')
                    ->where('workshop_id', $workshopId)
                    ->ignore($vehicle->id),
            ],
            'registration_number' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_id.exists' => 'Wybrany klient nie istnieje.',
            'vin.unique' => 'Pojazd z tym numerem VIN już istnieje w bazie danych.',
            'year.min' => 'Rok produkcji musi być większy lub równy 1900.',
            'year.max' => 'Rok produkcji nie może być większy niż 2100.',
        ];
    }
}
