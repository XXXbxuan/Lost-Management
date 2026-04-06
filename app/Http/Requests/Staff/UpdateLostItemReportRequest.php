<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLostItemReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'passenger_name' => ['required', 'string', 'max:255'],
            'passenger_email' => ['required', 'email', 'max:255'],
            'passenger_phone' => ['required', 'string', 'max:20'],
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:255'],
            'sub_colors' => ['nullable', 'array'],
            'sub_colors.*' => ['string', 'max:50'],
            'image' => ['nullable', 'image', 'max:2048'],
            'lost_location' => ['required', 'string', 'max:255'],
            'flight_number' => ['nullable', 'string', 'max:50'],
            'lost_time' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}