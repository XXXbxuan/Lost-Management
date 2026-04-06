<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class CompleteHandoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'claimerName' => ['required', 'string', 'max:255'],
            'claimerIcPassport' => ['required', 'string', 'max:50'],
            'handover_photo' => ['required', 'image', 'max:5120'],
            'handover_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}