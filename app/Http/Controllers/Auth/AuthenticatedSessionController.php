<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

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

        return $this->redirectAfterLogin(Auth::user());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $this->findOrCreateGoogleUser($googleUser);

            Auth::login($user);
            $request->session()->regenerate();

            return $this->redirectAfterLogin($user);
        } catch (Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google Login Failed.',
            ]);
        }
    }

    private function findOrCreateGoogleUser(SocialiteUser $googleUser): User
    {
        $googleEmail = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $googleName = $googleUser->getName() ?: 'Google User';

        $user = User::where('provider', 'google')
            ->where('provider_id', $googleId)
            ->first();

        if ($user) {
            $user->update([
                'name' => $googleName,
                'email' => $googleEmail,
                'email_verified_at' => now(),
            ]);

            return $user;
        }

        $user = User::where('email', $googleEmail)->first();

        if ($user) {
            $user->update([
                'name' => $googleName,
                'provider' => 'google',
                'provider_id' => $googleId,
                'email_verified_at' => now(),
            ]);

            return $user;
        }

        $baseUsername = Str::slug(explode('@', $googleEmail)[0] ?? 'googleuser', '');
        $baseUsername = $baseUsername !== '' ? $baseUsername : 'googleuser';
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return User::create([
            'username' => $username,
            'name' => $googleName,
            'email' => $googleEmail,
            'password' => Hash::make(Str::random(32)),
            'role' => 'Passenger',
            'points' => 0,
            'provider' => 'google',
            'provider_id' => $googleId,
            'email_verified_at' => now(),
        ]);
    }

    private function redirectAfterLogin(User $user): RedirectResponse
    {
        return redirect()
            ->route('dashboard')
            ->with('success', 'You have successfully logged in as ' . $user->role . '.');
    }
}