<?php

namespace App\Http\Requests\RepairOrders;

use App\Models\RepairOrder;
use Illuminate\Foundation\Http\FormRequest;

class ListMechanicRepairOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAnyMechanic', RepairOrder::class);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
