<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('toggle_status')) {
            return [];
        }

        $staff = $this->route('staff');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($staff->user_id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($staff->user_id),
            ],
            'contact_number' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:Admin,Staff'],
            'department' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', Password::defaults()],
        ];
    }
}