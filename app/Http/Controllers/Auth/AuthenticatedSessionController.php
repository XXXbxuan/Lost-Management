<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// --- NEW IMPORTS FOR GOOGLE LOGIN ---
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
     * Handle an incoming authentication request (Standard Login).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'Admin' || $user->role === 'Staff') { // Check Capitalization of Roles
             return redirect()->route('staff.dashboard'); 
        }

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

    // ==========================================
    //  GOOGLE LOGIN FUNCTIONS
    // ==========================================

    // 1. Send user to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Handle Google Response
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists (by email)
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // If user doesn't exist, create a new Passenger account
                $user = User::create([
                    'username' => $googleUser->getName(), // Use Google Name
                    'name'     => $googleUser->getName(), 
                    'email'    => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Random secure password
                    'role'     => 'Passenger',
                    'points'   => 0,
                ]);
            }

            // Log the user in
            Auth::login($user);

            // Redirect logic (Same as store method)
            if ($user->role === 'Admin' || $user->role === 'Staff') {
                return redirect()->route('staff.dashboard');
            }

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google Login Failed.']);
        }
    }
}