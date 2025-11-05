<?php

namespace App\Http\Requests\Vehicle;

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
}
