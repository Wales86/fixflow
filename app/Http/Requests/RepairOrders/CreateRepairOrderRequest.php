<?php

namespace App\Http\Requests\RepairOrders;

use App\Models\RepairOrder;
use Illuminate\Foundation\Http\FormRequest;

class CreateRepairOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', RepairOrder::class);
    }

    public function rules(): array
    {
        return [
            'preselected_vehicle_id' => 'sometimes|integer|exists:vehicles,id',
        ];
    }
}
