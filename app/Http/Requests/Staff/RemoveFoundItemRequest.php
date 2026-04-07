<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class RemoveFoundItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'removal_reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'removal_reason.required' => 'Please provide a removal reason.',
            'removal_reason.string' => 'The removal reason must be valid text.',
            'removal_reason.max' => 'The removal reason may not be greater than 1000 characters.',
        ];
    }
}