<?php

namespace App\Http\Requests\RepairOrders;

use App\Enums\RepairOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRepairOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('repairOrder'));
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => ['string'],
            'status' => [
                'string',
                Rule::enum(RepairOrderStatus::class),
            ],
        ];
    }
}
