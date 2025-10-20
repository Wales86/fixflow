<?php

namespace App\Http\Requests\RepairOrders;

use App\Enums\RepairOrderStatus;
use App\Models\RepairOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListRepairOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', RepairOrder::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(array_column(RepairOrderStatus::cases(), 'value'))],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'string', 'in:id,created_at,status,started_at,finished_at,total_time_minutes'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}
