<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OTPPasswordResetController extends Controller
{
    // 1. Show the form to enter email
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Send the code to email
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Delete old codes for this email
        PasswordResetCode::where('email', $request->email)->delete();

        // Generate a random 6-digit code
        $code = rand(100000, 999999);

        // Save to DB
        PasswordResetCode::create([
            'email' => $request->email,
            'code' => $code,
            // created_at is automatic if your model handles it, otherwise add:
            'created_at' => Carbon::now(),
        ]);

        // Send Email
        try {
            Mail::to($request->email)->send(new SendCodeResetPassword($code));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }

        return redirect()->route('password.verify', ['email' => $request->email]);
    }

    // 3. Show the form to enter OTP and New Password
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password-otp', ['email' => $request->email]);
    }

    // 4. Verify Code and Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if code exists and is valid (not older than 15 mins)
        $resetCode = PasswordResetCode::where('email', $request->email)
                                      ->where('code', $request->code)
                                      ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Invalid code.']);
        }
        
        // Optional: Check expiration manually if database doesn't handle it
        if (Carbon::parse($resetCode->created_at)->addMinutes(15)->isPast()) {
             return back()->withErrors(['code' => 'Code expired.']);
        }

        // Update User Password
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the code
        PasswordResetCode::where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully! You can now login.');
    }
}