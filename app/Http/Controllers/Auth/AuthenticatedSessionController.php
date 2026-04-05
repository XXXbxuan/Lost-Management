<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()
            ->route('dashboard')
            ->with('success', 'You have successfully logged in as ' . $user->role . '.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'username' => $googleUser->getName(),
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'role'     => 'Passenger',
                    'points'   => 0,
                ]);
            }

            Auth::login($user);

            return redirect()
                ->route('dashboard')
                ->with('success', 'You have successfully logged in as ' . $user->role . '.');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google Login Failed.',
            ]);
        }
    }
}