<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $targetUser = $this->route('user');

        return $this->user()->can('update', $targetUser);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
