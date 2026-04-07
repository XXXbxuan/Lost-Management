<?php

namespace App\Http\Requests\Passenger;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRescheduleProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'suggested_time_1' => ['required', 'date', 'after:now'],
            'suggested_time_2' => ['nullable', 'date', 'after:now'],
            'suggested_remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}