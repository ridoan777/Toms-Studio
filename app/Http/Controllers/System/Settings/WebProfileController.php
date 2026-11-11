<?php

namespace App\Http\Controllers\System\Settings;

use App\Helpers\FileHelpers\FileManagement;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\WebProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class WebProfileController extends Controller
{
	public function edit(Request $request): View
	{
		return view('Mastering.Profile.edit', [
			'user' => $request->user(),
		]);
	}


	public function update(WebProfileUpdateRequest $request): RedirectResponse
	{
		// dd($request->all());
		$user = $request->user();
		try {
			$DISK = config('filesystems.default');
			$validated = $request->validated();

			$oldAvatar = $user->avatar ?? null;

			// Handle file upload
			$avatarPath = FileManagement::handleFile(
				$request->file('avatar'),
				$validated['name'] ?? 'user',
				$oldAvatar,
				'user-avatars/admins',
				$DISK,
				$request->boolean('remove_image')
			);

			// Update user
			$user->fill([
				'name' => $validated['name'],
				'email' => $validated['email'],
				'birth_date' => $validated['birth_date'] ?? null,
				'address' => $validated['address'] ?? null,
				'avatar' => is_array($avatarPath) ? $avatarPath['path'] : $avatarPath,
				'about' => $validated['about'] ?? null,
			]);

			if ($user->isDirty('email')) {
				$user->email_verified_at = null;
			}

			$user->save();

			return Redirect::route('Mastering_Profile_edit')->with('success', 'Profile updated');
		} catch (Throwable $e) {
			Log::error('Profile update failed: ' . $e->getMessage(), [
				'file' => $e->getFile(),
				'line' => $e->getLine(),
			]);

			return back()->with('error', 'Something went wrong while updating your profile.' . $e->getMessage());
		}
	}


	public function destroy(Request $request): RedirectResponse
	{
		if (strtolower($request->user_role) === 'admin') {
			return redirect()->back()->with('error', "Admins cannot be deleted");
		}

		$request->validateWithBag('userDeletion', [
			'password' => ['required', 'current_password'],
		]);

		$user = $request->user();

		Auth::logout();

		$user->delete();

		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return Redirect::to('/');
	}
}
