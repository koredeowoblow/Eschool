<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Exceptions\HttpResponseException;

class  OtpService
{
    protected $expiryMinutes = 10; // OTP valid for 10 minutes

    /**
     * Generate and send OTP
     */
    public function send(string $email, string $type)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new HttpResponseException(get_error_response('User not found', 401));
        }

            // Generate OTP using a cryptographically secure random generator
            $otpCode = random_int(100000, 999999);

        // Save OTP
        Otp::updateOrCreate(
            [
                'email' => $email,
                'type' => $type,
            ],
            [
                'otp' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes($this->expiryMinutes),
            ]
        );

        // Send via Mail (or SMS if needed later)
        Mail::raw("Your OTP for {$type} is: {$otpCode}. It will expire in {$this->expiryMinutes} minutes.", function ($message) use ($email, $type) {
            $message->to($email)
                ->subject("OTP for {$type}");
        });

        return true;
    }

    public function SendWelcomeEmail(string $email, string $name)
    {
        Mail::raw("Welcome {$name}!\nYour account has been created.\nEmail: {$email}\nFor security reasons, we do not send passwords via email. Please use the 'Forgot Password' option on the login page to set your password.", function ($message) use ($email) {
            $message->to($email)
                ->subject("Welcome to Our Platform");
        });

        return true;
    }

    public function WelcomeStudent(string $email, string $name, string $guardianEmail, string $guardianName, string $schoolName)
    {
        // Email to Student
        Mail::raw("Welcome {$name}!\nYour student account has been created for {$schoolName}.\nEmail: {$email}\nFor security reasons, we do not send passwords via email. Please use the 'Forgot Password' option on the login page to set your password.", function ($message) use ($email, $schoolName) {
            $message->to($email)
                ->subject("Welcome to {$schoolName}");
        });

        // Email to Guardian
        Mail::raw("Welcome {$guardianName}!\nA guardian account has been created for your ward at {$schoolName}.\nEmail: {$guardianEmail}\nFor security reasons, we do not send passwords via email. Please use the 'Forgot Password' option on the login page to set your password.", function ($message) use ($guardianEmail, $schoolName) {
            $message->to($guardianEmail)
                ->subject("Guardian Account Created at {$schoolName}");
        });

        return true;
    }

    /**
     * Verify OTP
     */
    public function verify(string $email, string $otp, ?string $type = null)
    {
        $query = Otp::where('email', $email)
            ->where('otp', $otp);

        if ($type) {
            $query->where('type', $type);
        }

        $otpRecord = $query->first();

        if (!$otpRecord) {
            throw new HttpResponseException(get_error_response('Invalid OTP', 401));
        }

        if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
            throw new HttpResponseException(get_error_response('OTP expired', 401));
        }

        // OTP valid → delete it so it can’t be reused
        $otpRecord->delete();

        return true;
    }
}
