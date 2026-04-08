<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserAccountRequest extends FormRequest
{
    protected $errorBag = 'userDeletion';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->user()?->provider === 'google') {
            return [];
        }

        return [
            'password' => ['required', 'current_password'],
        ];
    }
}