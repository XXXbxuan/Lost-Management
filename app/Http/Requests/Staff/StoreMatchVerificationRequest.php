<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lost_id' => ['required'],
            'found_id' => ['required'],
            'outcome' => ['required', 'in:matched,not_matched'],
            'notes' => ['required', 'string', 'max:500'],
            'similarity_score' => ['nullable'],
            'return_url' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
        ];
    }
}