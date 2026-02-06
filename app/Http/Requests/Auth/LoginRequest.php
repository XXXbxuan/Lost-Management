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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Check Email & Password
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // ============================================================
        // 🟢 NEW: ROLE VALIDATION (Your Second Instruction)
        // ============================================================
        $user = Auth::user();
        $selectedRole = $this->input('login_role'); // Gets 'Passenger' or 'Staff' from the form

        // Case A: User selected "Passenger" tab, but is actually Staff/Admin
        if ($selectedRole === 'Passenger' && $user->role !== 'Passenger') {
            Auth::logout(); // Kick them out
            throw ValidationException::withMessages([
                'email' => 'This is a Staff account. Please switch to the Staff login tab.',
            ]);
        }

        // Case B: User selected "Staff" tab, but is actually a Passenger
        if ($selectedRole === 'Staff' && ($user->role !== 'Staff' && $user->role !== 'Admin')) {
            Auth::logout(); // Kick them out
            throw ValidationException::withMessages([
                'email' => 'Access Denied. Passengers cannot log in via the Staff portal.',
            ]);
        }
        // ============================================================

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
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

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
