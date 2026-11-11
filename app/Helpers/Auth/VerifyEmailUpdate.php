<?php

namespace App\Helpers\Auth;

use App\Helpers\ApiJsonReturnHelper;
use App\Mail\AlertMail;
use App\Models\Users\{User, UserVerification};
use App\Mail\SendOTPMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class VerifyEmailUpdate
{
	// public int $OTP_DIGIT_LENGTH = 4;
	// --------------------------------------
	public static function updateEmail($originalUser, $newEmail = null, $otpDuration = 10)
	{
		$emailChanged = isset($newEmail) && ($newEmail !== $originalUser->email);
		if ($emailChanged) {
			$existingUser = User::where('email', $newEmail)->where('id', '!=', $originalUser->id)->first();

			if ($existingUser) {
				return ApiJsonReturnHelper::handle(false, 422, 'This email is already taken.', null);
			}
			$secondEmail = $newEmail;

			// Delete any existing email change verification
			UserVerification::where('user_id', $originalUser->id)->where('type', 'email_change')->delete();

			// $otp = UserVerification::generateOTP(self::$OTP_DIGIT_LENGTH);
			$otp = UserVerification::generateOTP(config('settings.otp_digit_length', 4));

			UserVerification::create([
				'user_id' => $originalUser->id,
				'type' => 'email_change',
				'otp' => $otp,
				'new_value' => $secondEmail,
				'expiry_duration' => $otpDuration ?? null,
				'expires_at' => now()->addMinutes(10),
			]);

			/** @var \App\Models\Users\User|null $currentUser */
			$currentUser = auth()->user();

			$actionDoneByAdmin = $currentUser?->hasRoleKey('admin', 'super_admin');
			$adminChangeMessage = $actionDoneByAdmin ? "by {$currentUser->user_role} {$currentUser->name}." : null;
			
			// Send OTP to the OLD email for security information
			$DISK_FOLDER = config('filesystems.default');
			Mail::to($originalUser?->email ?? 'fallback@example.com')->send(new AlertMail($originalUser, "Email change alert", "Your email {$originalUser->email} has been under attempt to change into {$secondEmail} {$adminChangeMessage}.", "If you are not the one who did this, please change the password. Contact support!", true, $DISK_FOLDER, $originalUser->avatar));

			// // Send OTP to the NEW email for security
			Notification::route('mail', $secondEmail)->notify(new SendOTPMail($otp, 'email_verification', $otpDuration, $originalUser->name));

			return true;
		} else {
			return false;
		}
	}

	// --------------------------------------

}
/*
 Just call it as follows. No if-else is needed.

	$emailChanged = VerifyEmailUpdate::updateEmail($USER, $validated['email'], $this->OTP_EXPIRY_DURATION);
*/