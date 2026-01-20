<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. 验证账号密码
        $request->authenticate();

        // 2. 生成 Session
        $request->session()->regenerate();

        // ==================================================
        // [新增] 检查是否被封禁 (Block Check)
        // ==================================================
        $user = $request->user(); // 获取当前登录用户

        // 如果找到了对应的 Staff 档案，且状态是 Blocked
        if ($user->staff && $user->staff->status === 'Blocked') {
            
            // 马上强制登出
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 抛出错误信息，不让他进，并提示联系管理员
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Your account has been blocked. Please contact the administrator.',
            ]);
        }
        // ==================================================

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
