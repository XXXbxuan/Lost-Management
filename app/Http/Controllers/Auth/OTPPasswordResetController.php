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
    //Show the form to enter email
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    //Send the code to email
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        PasswordResetCode::where('email', $request->email)->delete();

        $code = rand(100000, 999999);

        PasswordResetCode::create([
            'email' => $request->email,
            'code' => $code,
            'created_at' => Carbon::now(),
        ]);

        try {
            Mail::to($request->email)->send(new SendCodeResetPassword($code));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }

        return redirect()->route('password.verify', ['email' => $request->email]);
    }

    //Show the form to enter OTP and New Password
    public function showResetForm(Request $request)
    {
        return view('auth.reset-password-otp', ['email' => $request->email]);
    }

    //Verify Code and Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetCode = PasswordResetCode::where('email', $request->email)
                                      ->where('code', $request->code)
                                      ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Invalid code.']);
        }
        
        if (Carbon::parse($resetCode->created_at)->addMinutes(15)->isPast()) {
             return back()->withErrors(['code' => 'Code expired.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        PasswordResetCode::where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successfully! You can now login.');
    }
}