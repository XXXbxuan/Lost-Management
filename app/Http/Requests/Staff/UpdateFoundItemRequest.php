<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoundItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'brand' => ['nullable', 'string', 'max:50'],
            'color' => ['required', 'string'],
            'sub_colors' => ['nullable', 'array'],
            'sub_colors.*' => ['string'],
            'serial_number' => ['nullable', 'string'],
            'found_location' => ['required', 'string'],
            'flight_number' => ['nullable', 'string', 'max:20'],
            'found_time' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'zone' => ['required', 'string'],
            'shelf' => ['required', 'string'],
            'slot' => ['required', 'string'],
            'storage_location' => ['required', 'string'],
            'finder_email' => ['nullable', 'email'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}