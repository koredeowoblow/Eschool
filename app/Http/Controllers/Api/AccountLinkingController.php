<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LinkingCodeNotification;
use Illuminate\Support\Str;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Auth\AccountLinkingRequest;

class AccountLinkingController extends Controller
{
    /**
     * Initiate account linking - send OTP to email.
     */
    public function initiate(AccountLinkingRequest $request)
    {
        $validated = $request->validated();

        $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::updateOrCreate(
            ['email' => $validated['email'], 'type' => 'account_linking'],
            [
                'otp' => $otpCode,
                'used' => false,
                'expires_at' => now()->addMinutes(15)
            ]
        );

        // Send Notification
        // In a real scenario, we'd find the user by email or just notify the email
        Notification::route('mail', $validated['email'])
            ->notify(new LinkingCodeNotification($otpCode));

        return ResponseHelper::success(null, 'A verification code has been sent to ' . $validated['email']);
    }

    /**
     * Verify OTP and link account.
     */
    public function verify(AccountLinkingRequest $request)
    {
        $validated = $request->validated();

        $otp = Otp::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->where('type', 'account_linking')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            abort(400, 'Invalid or expired verification code');
        }

        $otp->update(['used' => true]);

        // Logic for linking depends on what is being linked
        // For example: Linking a guardian's current user to a student
        // Here we just return success as a placeholder for the specific linking logic
        // which would typically happen here based on the user roles.

        return ResponseHelper::success(null, 'Account linked successfully!');
    }
}
