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

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $this->findOrCreateGoogleUser($googleUser);

            Auth::login($user);

            return $this->redirectAfterLogin($user);
        } catch (Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google Login Failed.',
            ]);
        }
    }

    private function findOrCreateGoogleUser(SocialiteUser $googleUser): User
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            return $user;
        }

        return User::create([
            'username' => $googleUser->getName(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'password' => Hash::make(Str::random(16)),
            'role' => 'Passenger',
            'points' => 0,
        ]);
    }

    private function redirectAfterLogin(User $user): RedirectResponse
    {
        return redirect()
            ->route('dashboard')
            ->with('success', 'You have successfully logged in as ' . $user->role . '.');
    }
}