<?php

namespace App\Http\Requests\TimeEntry;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class TimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            return $this->user()->can(UserPermission::CREATE_TIME_ENTRIES->value);
        }

        $timeEntry = $this->route('timeEntry');

        return $this->user()->workshop_id === $timeEntry->repairOrder->workshop_id
            && $this->user()->can(UserPermission::UPDATE_TIME_ENTRIES->value);
    }

    public function rules(): array
    {
        $workshopId = $this->user()->workshop_id;

        return [
            'repair_order_id' => [
                'required',
                'integer',
                "exists:repair_orders,id,workshop_id,{$workshopId}",
            ],
            'mechanic_id' => [
                'required',
                'integer',
                "exists:mechanics,id,workshop_id,{$workshopId},is_active,1",
            ],
            'duration_hours_input' => [
                'required',
                'integer',
                'min:0',
                'max:23',
            ],
            'duration_minutes_input' => [
                'required',
                'integer',
                'min:0',
                'max:59',
            ],
            'description' => [
                'nullable',
                'string',
                'max:65535',
            ],
        ];
    }
}
