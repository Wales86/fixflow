<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserPermission;

class FilterTeamReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(UserPermission::VIEW_REPORTS->value);
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
