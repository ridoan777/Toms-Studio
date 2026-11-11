<?php

namespace App\Http\Controllers\System\Settings;

use App\Helpers\{ApiJsonReturnHelper, Errors\ExceptionHandling, FileHelpers\FileManagement, PaymentHelpers\ReconciliationRevenueCat};
use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\ApiProfileUpdateRequest;
use App\Models\Users\{User, UserVerification};
use App\Mail\SendOTPMail;
use Illuminate\Support\Facades\{Storage, Validator, Hash, Notification, Auth, DB, Log, Redirect};
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Throwable;

class ApiProfileController extends Controller implements HasMiddleware
{
	use AuthorizesRequests;
	public static function middleware(): array
	{
		return [
			new Middleware(PermissionMiddleware::using('profile_index'), only: ['userProfileIndex']),
			new Middleware(PermissionMiddleware::using('profile_update'), only: ['userProfileUpdate']),
			new Middleware(PermissionMiddleware::using('profile_delete'), only: ['userProfileDelete']),
		];
	}
	private int $OTP_EXPIRY_DURATION = 15; // in minutes
	

	public function index(Request $request)
	{
		try {
			$USER = $request->user();

			// $reconcile = ReconciliationRevenueCat::handle($USER);
			// $USER->current_revenuecat_reconcile_attempt = $reconcile;

			return ApiJsonReturnHelper::handle(true, 200, 'My user data fetched successfully!', $USER);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('OPPS! User data fetching failed!', $e);
		}
	}


	public function update(ApiProfileUpdateRequest $request)
	{
		try {
			// dd($request->all());
			$user = $request->user();
			$DISK_FOLDER = config('filesystems.default');
			$validated = $request->validated();

			// ------------------- PASSWORD CHANGE -------------------
			if ($request->filled('password') || $request->filled('password_confirmation')) {
				$pwdValidator = Validator::make($request->only('password', 'password_confirmation'), [
					'password' => ['required', 'string', 'min:8', 'confirmed'],
				]);

				if ($pwdValidator->fails()) {
					return ApiJsonReturnHelper::handle(false, 422, 'Validation error.', null);
				}

				if (!Hash::check($request->current_password, $user->password)) {
					return ApiJsonReturnHelper::handle(false, 422, 'Current password is incorrect.', null);
				}

				$user->password = Hash::make($request->input('password'));
			}
			// ------------------- PASSWORD CHANGE -------------------

			// ------------------- IMAGE HANDLING -------------------
			$oldAvatar = $user?->avatar;
			$avatarPath = $oldAvatar;
			$REMOVE_AVATAR = $request->boolean('remove_avatar') ?? false;

			if ($request->hasFile('avatar') || $REMOVE_AVATAR) {
				$avatarPath = FileManagement::handleFile(
					$REMOVE_AVATAR ? null : $request->file('avatar'),
					($user->id) . '-' . ($validated['name'] ?? 'user') ?? 'user',
					$oldAvatar,
					'user-avatars',
					$DISK_FOLDER,
					$REMOVE_AVATAR
				);
			}
			// ------------------- IMAGE HANDLING -------------------

			$user->fill([
				'name' => $validated['name'] ?? $user->name,
				'phone' => $validated['phone'] ?? $user->phone,
				'avatar' => $avatarPath['path'] ?? $avatarPath,
				'timezone' => $validated['timezone'] ?? $user->timezone,
			]);

			$user->save();

			// ------------------- EMAIL UPDATE + SEND OTP -------------------
			$emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

			if ($emailChanged) {
				$existingUser = User::where('email', $validated['email'])->where('id', '!=', $user->id)->first();

				if ($existingUser) {
					return ApiJsonReturnHelper::handle(false, 422, 'This email is already taken.', null);
				}
			}

			if ($emailChanged) {
				$newEmail = $validated['email'];

				// Delete any existing email change verification
				UserVerification::where('user_id', $user->id)->where('type', 'email_change')->delete();

				$otp = UserVerification::generateOTP(config('settings.otp_digit_length'));

				UserVerification::create([
					'user_id' => $user->id,
					'type' => 'email_change',
					'otp' => $otp,
					'new_value' => $newEmail,
					'expiry_duration' => $this->OTP_EXPIRY_DURATION ?? null,
					'expires_at' => now()->addMinutes($this->OTP_EXPIRY_DURATION),
				]);

				// Send OTP to the NEW email using Notification facade
				Notification::route('mail', $newEmail)->notify(new SendOTPMail($otp, 'email_verification', $this->OTP_EXPIRY_DURATION));
			}
			// ------------------- EMAIL UPDATE + SEND OTP -------------------

			$user->avatar_url = $user->avatar ? Storage::url($user->avatar) : null;

			$MESSAGE = $emailChanged ? 'Profile updated. Please verify your new email using the OTP sent to ' . $validated['email']
				: 'Profile updated successfully.';

			return ApiJsonReturnHelper::handle(true, 200, $MESSAGE, [
				'user' => $user,
				'email_verification_required' => $emailChanged,
			]);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('updating profile', $e);
		}
	}


	public function delete(Request $request)
	{
		$request->validate([
			'password' => 'required|string',
		]);

		$name = '';
		$user = Auth::user();
		$DISK_FOLDER = config('filesystems.default');

		if (!$user || strtolower($user->user_role) === 'admin') {
			return ApiJsonReturnHelper::handle(false, 401, 'Unauthorized! The user does not exist or does not have permission to perform this action from here.', null);
		}

		if (!Hash::check($request->password, $user->password)) {
			return ApiJsonReturnHelper::handle(false, 401, 'Password confirmation failed.', null);
		}

		try {
			DB::beginTransaction();

			$name = $user->name;

			if ($user->avatar) {
				FileManagement::deleteFile($user->avatar, $DISK_FOLDER);
			}

			$user->tokens()->delete(); // removes API tokens

			$user->delete();

			DB::commit();

			return ApiJsonReturnHelper::handle(true, 200, "The account of {$name} and associated avatar have been deleted successfully.", null);
		} catch (Exception $e) {
			DB::rollBack();
			return ExceptionHandling::handle('deleting account.', $e);
		}
	}


	// ----------------- UPDATE EMAIL ------------------
	public function resendUpdateOTP(Request $request)
	{
		try {
			$validated = $request->validate([
				'honeypot' => 'nullable|string|in:',
				'email' => 'required|email|unique:users,email', // <-- change here
				'type' => 'required|in:email_change',
			], [
				'honeypot.in' => 'Bot attack prevention activated! Wait for a while!',
			]);

			$user = Auth::user();

			$userVerification = UserVerification::where('user_id', $user->id)
				->where('type', $validated['type'])
				->latest()
				->first();

			if ($userVerification && $userVerification->created_at->diffInSeconds(now()) < 60) {
				return ApiJsonReturnHelper::handle(false, 429, 'Please wait a minute before requesting a new OTP.', null);
			}

			$otp = UserVerification::generateOTP(config('settings.otp_digit_length'));

			if (!$userVerification) {
				$userVerification = UserVerification::create([
					'user_id' => $user->id,
					'type' => $validated['type'],
					'otp' => $otp,
					'new_value' => $validated['email'],
					'expiry_duration' => $this->OTP_EXPIRY_DURATION ?? null,
					'expires_at' => Carbon::now()->addMinutes($this->OTP_EXPIRY_DURATION),
				]);
			} else {
				$userVerification->update([
					'otp' => $otp,
					'new_value' => $validated['email'],
					'expires_at' => Carbon::now()->addMinutes(10),
				]);
			}

			Notification::route('mail', $userVerification->new_value)
				->notify(new SendOTPMail($otp, $validated['type'], 10));

			return ApiJsonReturnHelper::handle(true, 200, 'OTP sent successfully.', null);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('sending update email OTP', $e);
		}
	}


	public function confirmNewEmail(Request $request)
	{
		try {
			// Validate OTP
			$validated = $request->validate([
				'otp' => ['required', 'string', 'size:' . config('settings.otp_digit_length')],
			]);

			$user = $request->user();

			// Find the verification record for this user and OTP
			$verification = UserVerification::where('user_id', $user->id)
				->where('type', 'email_change')
				->where('otp', $validated['otp'])
				->first();

			// Check OTP existence
			if (!$verification) {
				return ApiJsonReturnHelper::handle(false, 422, 'Invalid OTP.', null);
			}

			// Check OTP expiry
			if ($verification->isExpired()) {
				return ApiJsonReturnHelper::handle(false, 409, 'OTP has expired.', null);
			}

			// Check if the new email is already taken
			$newEmail = $verification->new_value;
			$existingUser = User::where('email', $newEmail)
				->where('id', '!=', $user->id)
				->first();

			if ($existingUser) {
				$verification->delete(); // delete old OTP
				return ApiJsonReturnHelper::handle(false, 422, 'This email is no longer available.', null);
			}

			// Update user's email
			$user->update([
				'email' => $newEmail,
				'email_verified_at' => now(),
			]);

			// Delete the OTP record
			$verification->delete();

			return ApiJsonReturnHelper::handle(true, 200, 'Email updated successfully.', null);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('updating email', $e);
		}
	}
}
