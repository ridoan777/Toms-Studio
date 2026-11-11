<?php

namespace App\Http\Controllers\System\Notifications;

use App\Helpers\{Errors\ExceptionHandling,ApiJsonReturnHelper};
use App\Helpers\Auth\OwnershipAuthCheck;
use App\Http\Controllers\Controller;
use App\Models\System\Notifications\InAppNotification;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\Http\Request;
use Throwable;

class ApiInAppNotificationController extends Controller
{
	public function index(Request $request)
	{
		try {
			$user = $request->user();

			$notifications = InAppNotification::where('user_id', $user->id)->status(1)->latest()->paginate(20);

			return ApiJsonReturnHelper::handle(true, 200, 'In-App-Notifications have been fetched successfully!.', $notifications);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching in-app-notifications.', $e);
		}
	}

	public function delete(Request $request, InAppNotification $notify)
	{
		$name = null;
		$user = $request->user();
		try {
			OwnershipAuthCheck::ownerVsOwner($notify->user_id, $user->id);

			$name = ($notify?->type ?? '') . ' ' . ($notify?->severity ?? '') . " of id:" . ($notify?->id ?? '');
			$notify->delete();

			return ApiJsonReturnHelper::handle(true, 200, "In-App-Notifications '{$name}' has been deleted successfully!.", null);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('deleting an in-app-notifications.', $e);
		}
	}
}
