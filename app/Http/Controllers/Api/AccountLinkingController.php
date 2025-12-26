<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LinkingCodeNotification;
use Illuminate\Support\Str;

class AccountLinkingController extends Controller
{
    /**
     * Initiate account linking - send OTP to email.
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::updateOrCreate(
            ['email' => $request->email, 'type' => 'account_linking'],
            [
                'otp' => $otpCode,
                'used' => false,
                'expires_at' => now()->addMinutes(15)
            ]
        );

        // Send Notification
        // In a real scenario, we'd find the user by email or just notify the email
        Notification::route('mail', $request->email)
            ->notify(new LinkingCodeNotification($otpCode));

        return get_success_response(null, 'A verification code has been sent to ' . $request->email);
    }

    /**
     * Verify OTP and link account.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('type', 'account_linking')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return get_error_response('Invalid or expired verification code', 400);
        }

        $otp->update(['used' => true]);

        // Logic for linking depends on what is being linked
        // For example: Linking a guardian's current user to a student
        // Here we just return success as a placeholder for the specific linking logic
        // which would typically happen here based on the user roles.

        return get_success_response(null, 'Account linked successfully!');
    }
}
