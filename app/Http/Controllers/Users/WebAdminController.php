<?php
# this controller will only contain 1st parent (or, visible contents) of admin dashboard. For nested or children, they should have separate controllers and route files

namespace App\Http\Controllers\Users;

use App\Helpers\Auth\OwnershipAuthCheck;
use App\Http\Controllers\Controller;
use App\Models\Users\{User, UserVerification};
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\SendOTPMail;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Exception;

class WebAdminController extends Controller implements HasMiddleware
{
	private int $OTP_EXPIRY_DURATION = 15;
	
	public static function middleware(): array
	{
		return [
			// new Middleware('verify_role_key:admin',	 only: ['allProjects']),
			// new Middleware(PermissionMiddleware::using(['admin_index', 'admin_show', 'admin_create', 'admin_update', 'admin_delete']), only: ['deleteAttempt']),
		];
	}
	// ---------------USERS---------------
	public function verifyEmail(Request $request)
	{
		try {
			$validated = $request->validate([
				'flag' => 'required|string|in:email_change',
				'user_id' => 'required|integer|exists:user_verifications,user_id',
				'otp' => ['required', 'string', 'size:' . config('settings.otp_digit_length')],
			]);
			$TARGET_USER = null;

			$userVerifiaction = UserVerification::userId($validated['user_id'])->type('email_change')->latest()->first();
			$TARGET_USER = User::findOrFail((int)$userVerifiaction?->user_id);

			if ($userVerifiaction && $userVerifiaction?->isExpired()) {
				return redirect()->back()->with('error', 'OTP expired. Resend OTP!');
			}

			if ((int)$validated['otp'] !== (int)$userVerifiaction?->otp) {
				return redirect()->back()->with('error', 'OTP mismatched! Try again!');
			}
			$TARGET_USER->fill([
				'email' => $userVerifiaction?->new_value,
				'email_verified_at' => now(),
			]);
			$TARGET_USER->save();

			if ($TARGET_USER) {
				$userVerifiaction?->delete();
			}

			return redirect()->back()->with('success', "OTP verified. Email updated successfully!");
		} catch (Exception $e) {
			return redirect()->back()->with('error', "Verification failed. Please try again later." . $e->getMessage());
		}
	}

	public function resendOTP(Request $request)
	{
		$validated = $request->validate([
			'flag' => 'required|string|in:resend_verify_email',
			'user_id' => 'required|integer|exists:user_verifications,user_id',
		]);

		$OldUserVerifiaction = UserVerification::userId($validated['user_id'])->type('email_change')->latest()->first();
		$targetUser = User::findOrFail((int)$OldUserVerifiaction?->user_id);

		$otp = UserVerification::generateOTP(config('settings.otp_digit_length'));

		UserVerification::create([
			'user_id' => $targetUser->id,
			'type' => "email_change",
			'otp' => $otp,
			'new_value' => $OldUserVerifiaction?->new_value,
			'expiry_duration' => $this->OTP_EXPIRY_DURATION ?? null,
			'expires_at' => Carbon::now()->addMinutes($this->OTP_EXPIRY_DURATION),
		]);

		$OldUserVerifiaction->delete();

		$targetUser->notify(new SendOTPMail($otp, "email_change", $this->OTP_EXPIRY_DURATION));

		return redirect()->back()->with('success', "A new OTP has been sent. Check the old email!");
	}

	public function deleteAttempt(Request $request, UserVerification $userVerification)
	{
		try {
			$name = $userVerification ? $userVerification?->new_value : null;
			$USER = $request->user();
			if(!$USER->hasRoleKey('admin', 'super_admin')){
				OwnershipAuthCheck::ownerVsOwner((int)$userVerification?->user_id, $USER?->id);
			}
			$userVerification->delete();

			return redirect()->back()->with('success', "User verification attempt for changing the email '{$name}' has been revoked successfully!");
		} catch (Exception $e) {
			return redirect()->back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
		}
	}
}
