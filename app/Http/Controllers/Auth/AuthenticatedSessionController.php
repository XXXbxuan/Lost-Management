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
        // 1. Authenticate the user (Checks email & password automatically)
        $request->authenticate();

        // 2. Regenerate session ID (Security practice)
        $request->session()->regenerate();

        // 3. Redirect Logic
        $user = Auth::user();

        // If you have roles, you can redirect them specifically here
        if ($user->role === 'admin' || $user->role === 'staff') {
             // Make sure this route exists in your web.php
             return redirect()->route('staff.dashboard'); 
        }

        // Standard Users go to the Dashboard
        return redirect()->intended(route('dashboard'));
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