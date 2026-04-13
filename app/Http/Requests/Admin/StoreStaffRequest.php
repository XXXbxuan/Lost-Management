<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreStaffRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'string', 'max:20'],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:Admin,Staff'],
            'department' => ['nullable', 'string', 'max:100'],
        ];
    }
}