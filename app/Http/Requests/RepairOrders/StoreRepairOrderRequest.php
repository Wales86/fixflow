<?php

namespace App\Http\Requests\RepairOrders;

use App\Models\RepairOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRepairOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', RepairOrder::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workshopId = $this->user()->workshop_id;

        return [
            'vehicle_id' => [
                'required',
                'integer',
                Rule::exists('vehicles', 'id')->where('workshop_id', $workshopId),
            ],
            'description' => ['required', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => [
                'file',
                'mimes:jpeg,png,jpg,gif',
                'max:10240',
            ],
        ];
    }
}
