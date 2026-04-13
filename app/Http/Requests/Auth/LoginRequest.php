<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'login_role' => ['nullable', 'string'], 
        ];
    }

   
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $selectedRole = $this->input('login_role'); 


        if ($selectedRole === 'Passenger' && $user->role !== 'Passenger') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'This is a Staff account. Please switch to the Staff login tab.',
            ]);
        }

        if ($selectedRole === 'Staff' && !in_array($user->role, ['Staff', 'Admin'])) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Access Denied. Passengers cannot log in via the Staff portal.',
            ]);
        }


        if ($user->role === 'Staff' || $user->role === 'Admin') {
            
            if ($user->staff && $user->staff->status === 'Blocked') {
                Auth::logout(); 

                throw ValidationException::withMessages([
                    'email' => 'SECURITY ALERT: Your access has been revoked. (Account Blocked)',
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }


    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }


    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}