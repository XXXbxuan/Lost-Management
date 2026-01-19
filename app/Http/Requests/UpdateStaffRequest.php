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
        // 获取路由里的 staff 对象 (知道现在改的是谁)
        $staff = $this->route('staff');

        return [
            'name' => ['required', 'string', 'max:255'],
            
            // 下面这两行用了 Rule::unique...->ignore()
            // 意思是：检查重复时，要把“我自己”排除在外，不然自己不改名也会报错
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($staff->user_id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->user_id)],
            
            'contact_number' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:Admin,Staff'],
            'department' => ['nullable', 'string', 'max:100'],
            
            // 密码是可选的：如果不填(nullable)，就不改密码；如果填了，就必须符合密码规则
            'password' => ['nullable', Password::defaults()], 
        ];
    }
}
