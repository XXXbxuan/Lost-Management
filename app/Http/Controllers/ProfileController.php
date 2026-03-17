<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    // Display the user's profile form
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Update the user's profile information.
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 使用你現有的 ProfileUpdateRequest 規則
        $validated = $request->validated();

        $emailChanged = isset($validated['email']) && ($validated['email'] !== $user->email);

        $user->fill($validated);

        // Email changed → mark unverified
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Email changed → auto send verification email
        if ($emailChanged && $user instanceof MustVerifyEmail) {
            try {
                $user->sendEmailVerificationNotification();
                return Redirect::route('profile.edit')->with('status', 'verification-link-sent');
            } catch (\Throwable $e) {
                // 如果你要 debug 可以開這行
                // \Log::error('Verification email send failed', ['error' => $e->getMessage()]);
                return Redirect::route('profile.edit')->with('status', 'verification-link-failed');
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Delete the user's account.
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}