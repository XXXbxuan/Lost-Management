<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
// [预防性修复] 显式引用 Password 类，防止等下报找不到类的错
use Illuminate\Validation\Rules\Password;

class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->has('toggle_status')) {
            return [];
        }
        // 获取路由里的 staff 对象 (知道现在改的是谁)
        $staff = $this->route('staff');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($staff->user_id)],
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($staff->user_id)],
            'contact_number' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:Admin,Staff'],
            'department' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', \Illuminate\Validation\Rules\Password::defaults()],
        ];
    }
}
