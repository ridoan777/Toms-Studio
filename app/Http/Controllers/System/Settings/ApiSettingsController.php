<?php

namespace App\Http\Controllers\System\Settings;

use App\Helpers\ApiJsonReturnHelper;
use App\Helpers\Errors\ExceptionHandling;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\AppPreferencesRequest;
use App\Models\System\Settings\{OptionEmailTemplate, OptionEnv, OptionSiteSetup, OptionStaticPage, UserAppPreference};
use Throwable;

class ApiSettingsController extends Controller
{
	public function indexStaticPages()
	{
		try {
			$staticPage = OptionStaticPage::status(1)->get();

			return ApiJsonReturnHelper::handle(true, 200, 'Static page data fetched successful!', ['static_page' => $staticPage]);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching static page data', $e);
		}
	}
	// --------------------------------------------------

	public function basicSiteSetup()
	{
		try {
			$siteSetup = OptionSiteSetup::where('type', "site_basic")->status(1)->get();

			return ApiJsonReturnHelper::handle(true, 200, 'Basic site data has been fetched successfully!', ['website' => $siteSetup]);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('fetching projects', $e);
		}
	}
	// --------------------------------------------------

	public function preferenceStore(AppPreferencesRequest $request)
	{
		try {
			$USER = $request->user();
			$validated = $request->validated();

			$appPreference = UserAppPreference::updateOrCreate(
				[
					'user_id' => $USER->id,
				],
				[
					'in_app_notification' => $validated['in_app_notification'] ?? 1,
					'email_notification' => $validated['email_notification'] ?? 1,
					'push_notification' => $validated['push_notification'] ?? 1,
					'activity_log' => $validated['activity_log'] ?? 1,
					'status' => $validated['status'] ?? 1,
				]
			);

			$appPreference->load('appPreferenceRelatingBackTo_user:id,name,email');
			$appPreference->setRelation('current_user', $appPreference->appPreferenceRelatingBackTo_user);
			$appPreference->unsetRelation('appPreferenceRelatingBackTo_user');

			return ApiJsonReturnHelper::handle(true, 200, 'User app preferences have been saved!', $appPreference);
		} catch (Throwable $e) {
			return ExceptionHandling::handle('saving user app preference.', $e);
		}
	}
	// --------------------------------------------------
}
