<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class FilterTeamReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date_format:Y-m-d\TH:i:s.v\Z'],
            'end_date' => ['nullable', 'date_format:Y-m-d\TH:i:s.v\Z', 'after_or_equal:start_date'],
        ];
    }
}
