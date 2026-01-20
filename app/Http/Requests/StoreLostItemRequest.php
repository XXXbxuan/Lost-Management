<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLostItemRequest extends FormRequest
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
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'brand' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string'],
            'serial_number' => ['nullable', 'string'],
            
            'found_location' => ['required', 'string'], // 下拉菜单的值
            'flight_number' => ['nullable', 'string', 'max:20'], // 新增
            
            'found_time' => ['required', 'date', 'before_or_equal:now'],
            'description' => ['nullable', 'string'],
            
            'storage_location' => ['nullable', 'string'], // 新增：我们生成的 GEN-S1-01
            
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
        ];
    }
}
