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
     * 權限驗證：通常設為 true 允許所有人提交登入請求
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 驗證規則：確保 email, password 和 login_role 都有傳入
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'login_role' => ['nullable', 'string'], // 用於分流 Passenger 和 Staff
        ];
    }

    /**
     * 🌟 核心驗證邏輯：包含密碼、角色分流與 Blocked 狀態檢查
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1) 先驗證帳號密碼是否正確
        // 如果密碼不對，Laravel 會自動在這裡攔截並拋出錯誤
        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // 登入成功後，獲取用戶實例進行後續安全檢查
        $user = Auth::user();
        $selectedRole = $this->input('login_role'); 

        // 2) 角色與登入頁面分流驗證
        // 防止 Staff 跑到乘客頁面登入，反之亦然
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

        // 3) ✅ 核心封鎖檢查：對應你的 Staff Table 狀態
        // 只要不是 Passenger，我們就檢查他的 Staff Profile 狀態是否為 'Blocked'
        if ($user->role === 'Staff' || $user->role === 'Admin') {
            
            // 這裡使用了 $user->staff 關聯，請確保你的 User Model 裡有定義 public function staff()
            if ($user->staff && $user->staff->status === 'Blocked') {
                Auth::logout(); // 密碼雖然對，但因為被封鎖，強制踢出 Session

                throw ValidationException::withMessages([
                    'email' => 'SECURITY ALERT: Your access has been revoked. (Account Blocked)',
                ]);
            }
        }

        // 登入成功，清除錯誤計數
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * 限制登入頻率，防止暴力破解
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
     * 定義限流的唯一標識（Email + IP）
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}