<?php

namespace App\Domains\Users\Services;

use App\Mail\SendOTPMail;
use App\Models\Users\User;
use App\Models\Users\UserVerification;
use Throwable;

class UserVerificationService
{
	public function sendOTP(User $user, string $type, int $otpDuration)
	{
		UserVerification::where('user_id', $user->id)->where('type', $type)->delete();

		$otp = UserVerification::generateOTP(config('settings.otp_digit_length'));

		UserVerification::create([
			'user_id' => $user->id,
			'type' => $type,
			'otp' => $otp,
			'expiry_duration' => $otpDuration,
			'expires_at' => now()->addMinutes($otpDuration),
		]);
      // logger('otp service class reached for ' . $type .'.........', [$otp]);
		$user->notify(new SendOTPMail($otp, $type, $otpDuration));

		return $otp;
	}
}
/*
// ---------------------- USER VERIFICATION OTP ----------------------
$otp = app(UserVerificationService::class)->sendOTP($user, 'password_reset', $this->OTP_EXPIRY_DURATION);
// ---------------------- USER VERIFICATION OTP ----------------------
*/