<?php

namespace App\Http\Requests\Report;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class FilterMechanicReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'mechanic_id' => ['nullable', 'integer', 'exists:mechanics,id'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->startOfDay() : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->endOfDay() : null,
        ]);
    }
}
