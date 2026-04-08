<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordWithOtpRequest;
use App\Http\Requests\Auth\SendOtpResetCodeRequest;
use App\Mail\SendCodeResetPassword;
use App\Models\PasswordResetCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class OTPPasswordResetController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(SendOtpResetCodeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $email = $validated['email'];

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'No account found for this email address.',
                ])
                ->withInput();
        }

        PasswordResetCode::where('email', $email)->delete();

        $code = random_int(100000, 999999);

        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'created_at' => Carbon::now(),
        ]);

        try {
            Mail::to($email)->send(new SendCodeResetPassword($code));
        } catch (Throwable $e) {
            return back()->withErrors([
                'email' => 'Failed to send email.',
            ]);
        }

        return redirect()->route('password.verify', ['email' => $email]);
    }

    public function showVerificationForm(Request $request): View
    {
        return view('auth.reset-password-otp', [
            'email' => $request->email,
        ]);
    }

    public function resetPassword(ResetPasswordWithOtpRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $resetCode = PasswordResetCode::where('email', $validated['email'])
            ->where('code', $validated['code'])
            ->first();

        if (!$resetCode) {
            return back()->withErrors([
                'code' => 'Invalid code.',
            ]);
        }

        if (Carbon::parse($resetCode->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors([
                'code' => 'Code expired.',
            ]);
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            PasswordResetCode::where('email', $validated['email'])->delete();

            return redirect()->route('password.request')->withErrors([
                'email' => 'This account no longer exists.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        PasswordResetCode::where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully! You can now login.');
    }
}