<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoundItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 允许所有人通过
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'brand' => ['nullable', 'string', 'max:50'],
            'color' => ['required', 'string'], // 这里改成了 required 因为我们要选颜色
            'sub_colors' => ['nullable', 'array'], // 多选颜色的数组
            'serial_number' => ['nullable', 'string'],
            'found_location' => ['required', 'string'],
            'flight_number' => ['nullable', 'string', 'max:20'],
            'found_time' => ['required', 'date', 'before_or_equal:now'],
            'description' => ['nullable', 'string'],
            'storage_location' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
        ];
    }
}