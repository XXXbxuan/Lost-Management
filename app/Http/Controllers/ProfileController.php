<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteUserAccountRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged && $user instanceof MustVerifyEmail) {
            try {
                $user->sendEmailVerificationNotification();

                return Redirect::route('profile.edit')->with('status', 'verification-link-sent');
            } catch (Throwable $e) {
                return Redirect::route('profile.edit')->with('status', 'verification-link-failed');
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(DeleteUserAccountRequest $request): RedirectResponse
    {
        $request->validated();

        $user = $request->user();

        DB::transaction(function () use ($user) {
            $suffix = now()->format('YmdHis') . '_' . $user->id;

            $user->update([
                'email' => 'deleted_' . $suffix . '_' . $user->email,
                'username' => $user->username . '_deleted_' . $suffix,
                'provider_id' => $user->provider === 'google' && $user->provider_id
                    ? $user->provider_id . '_deleted_' . $suffix
                    : $user->provider_id,
            ]);

            $user->delete();
        });

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}