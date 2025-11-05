<?php

namespace App\Http\Requests\RepairOrders;

use App\Enums\RepairOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateRepairOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', $this->route('repairOrder'));
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(RepairOrderStatus::class)],
            'mechanic_id' => [
                'nullable',
                'integer',
                Rule::exists('mechanics', 'id')->where(function ($query) {
                    $query->where('workshop_id', $this->user()->workshop_id);
                }),
            ],
        ];
    }
}
