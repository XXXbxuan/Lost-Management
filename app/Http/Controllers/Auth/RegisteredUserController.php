<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validate 'username' instead of 'name'
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], // 🟢 Check username
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Create User using 'username'
        $user = User::create([
            'username' => $request->username, // 🟢 Save username
            'name' => null,                   // Name is optional now
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Passenger',            // Default role
            'points' => 0,                    // Default points
        ]);

        event(new Registered($user));

        // 3. Do NOT login automatically (as requested previously)
        // Auth::login($user);

        // 4. Redirect to Login
        return redirect()->route('login')->with('status', 'Registration successful! Please log in.');
    }
}