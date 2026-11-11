<?php

namespace App\Http\Controllers\System\Settings;

use App\Domains\Billings\Enums\CurrencyEnums;
use App\Helpers\{Errors\ExceptionHandling, FileHelpers\FileManagement};
use App\Http\Controllers\Controller;
use App\Models\Billings\Subscriptions\SubscriptionTier;
use App\Models\Communication\Chatting\{ChatGallery, ChatGroup};
use App\Models\System\Settings\{OptionEmailTemplate, OptionEnv, OptionSiteSetup, OptionStaticPage};
use App\Models\Users\{User, UserVerification};
use App\Models\Workflows\Lessons\LessonNoteGallery;
use App\Models\Workflows\Projects\{Project, ProjectGallery};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Artisan, Auth, Cache, DB, Log, Storage};
use Illuminate\Validation\Rule;
use Stripe\Checkout\Session;
use Laravel\Cashier\Cashier;
use Stripe\Stripe;
use Throwable;
use Exception;

class WebSettingsController extends Controller
{
	public function index()
	{
		return view('System.Settings.web_settings');
	}

	// ---------------------- PURGING ----------------------
	public function indexPurging()
	{
		return view('System.Settings.purging');
	}

	public function purgingUnapproved()
	{
		try {
			$DISK_FOLDER = config('filesystems.default');
			// $CUTOFF = Carbon::now()->subDays(3);
			$CUTOFF = Carbon::now()->copy()->subDays(3);


			$unverifiedNewUsers = User::whereNull('email_verified_at')->status(0)->where('created_at', '<=', $CUTOFF)->get();
			$unverifiedOldUsers = UserVerification::where('expires_at', '<=', $CUTOFF)->get();
			// $unverifiedOldUsers = UserVerification::where('created_at', '<=', Carbon::now())->get();

			if ($unverifiedNewUsers->isEmpty() && $unverifiedOldUsers->isEmpty()) {
				return back()->with('info', 'No expired or unverified records found for purging.');
			}

			DB::beginTransaction();

			foreach ($unverifiedNewUsers as $user) {
				if (strtolower($user->user_role) === 'admin') {
					continue;
				}

				// Delete avatar safely
				FileManagement::deleteFile($user->avatar, $DISK_FOLDER);

				// Delete user record
				$user->delete();
			}
			$unverifiedOldUsers = UserVerification::where('expires_at', '<=', $CUTOFF)->delete();
			// $unverifiedOldUsers = UserVerification::where('created_at', '<=', Carbon::now())->delete();

			if (($unverifiedNewUsers->count() + $unverifiedOldUsers) === 0) {
				return back()->with('info', 'No expired or unverified records found for purging.');
			}

			DB::commit();

			return back()->with(
				'success',
				"{$unverifiedNewUsers->count()} unverified new user(s) & a total of " . ($unverifiedNewUsers->count() + $unverifiedOldUsers) . " record(s) purged successfully."
			);
		} catch (Throwable $e) {
			DB::rollBack();

			return back()->with('error', 'Purge failed: ' . $e->getMessage());
		}
	}

	public function purgingDiskCleanup()
	{
		try {
			$disk = config('filesystems.default');

			//  1. Collect all files from storage
			$allFiles = collect(Storage::disk($disk)->allFiles());

			//  2. Collect all valid file paths from database
			$validPaths = collect()
				->merge(User::pluck('avatar'))
				->merge(OptionStaticPage::pluck('feature_image'))
				->merge(Project::pluck('image'))
				->merge(Project::pluck('video'))
				->merge(ProjectGallery::pluck('file'))
				->merge(ChatGallery::pluck('file'))
				->merge(ChatGroup::pluck('image'))
				->merge(SubscriptionTier::pluck('image'))
				->merge(LessonNoteGallery::pluck('filepath'))
				->filter()
				->map(fn($path) => str_replace('\\', '/', $path))
				->unique()
				->values();

			//  3. Determine junk files (exist in disk but not in DB)
			$junkFiles = $allFiles
				->reject(fn($file) => $validPaths->contains($file))
				->filter(fn($file) => !str_starts_with($file, '.')) // ignore .gitignore, etc.
				->values();

			if ($junkFiles->isEmpty()) {
				return back()->with('info', 'No junk files found for purging.');
			}

			DB::beginTransaction();

			//  4. Delete junk files
			foreach ($junkFiles as $file) {
				if (Storage::disk($disk)->exists($file)) {
					// optional: your custom helper for tracking/logging
					FileManagement::deleteFile($file, $disk);
				}
			}

			DB::commit();

			//  5. Now clean up empty directories
			$allDirs = collect(Storage::disk($disk)->allDirectories())->sortByDesc(fn($dir) => substr_count($dir, '/')); // deepest first

			$deletedDirs = collect();

			foreach ($allDirs as $dir) {
				// allFiles() checks recursively, ensures no nested content left
				if (empty(Storage::disk($disk)->allFiles($dir))) {
					Storage::disk($disk)->deleteDirectory($dir);
					$deletedDirs->push($dir);
				}
			}

			//  6. Return success message
			return back()->with(
				'success',
				"Disk cleanup completed successfully. " .
				"Deleted {$junkFiles->count()} junk file(s) and {$deletedDirs->count()} empty directorie(s)."
			);

		} catch (Throwable $e) {
			DB::rollBack();
			Log::error('Disk cleanup failed', ['error' => $e->getMessage()]);
			return back()->with('error', 'Disk cleanup failed: ' . $e->getMessage());
		}
	}
	// ---------------------- PURGING ----------------------


	// ---------------------- STATIC PAGES ----------------------

	public function indexStaticPages()
	{
		$aboutUs = OptionStaticPage::where('type', 'about_us')->first();
		$terms = OptionStaticPage::where('type', 'term_n_conditions')->first();
		$privacyPolicy = OptionStaticPage::where('type', 'privacy_policy')->first();
		$helpSupport = OptionStaticPage::where('type', 'help_n_support')->first();

		return view('System.Settings.static_page_index', compact('aboutUs', 'terms', 'privacyPolicy', 'helpSupport'));
	}

	public function storeStaticPages(Request $request)
	{
		try {
			$validated = $request->validate([
				'id' => 'nullable|integer|exists:option_static_pages,id',
				'type' => 'required|string|max:100',          // e.g. about_us, privacy_policy
				'title' => 'nullable|string|max:255',
				'body' => 'required|string',
				'feature_image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
				'remove_image' => 'nullable|boolean',
				'status' => 'required|boolean',
			]);
			$status = (int)$validated['status'];
			$DISK_FOLDER = config('filesystems.default');

			$existingPage = !empty($validated['id']) ? OptionStaticPage::find($validated['id']) : null;

			if ($request->feature_image) {
				$IMAGE_PATH = FileManagement::handleFile(
					$request->file('feature_image') ?? null,
					$validated['title'] ?? 'static_page',
					$existingPage->feature_image ?? null,
					'Settings/static-pages',
					$DISK_FOLDER,
					(bool)($validated['remove_image'] ?? false)
				);
			}

			// Create or update record
			$page = OptionStaticPage::updateOrCreate(
				['id' => $validated['id'] ?? null],
				[
					'type' => $validated['type'],
					'title' => $validated['title'],
					'body' => $validated['body'] ?? null,
					'feature_image' => $IMAGE_PATH ?? null,
					'status' => $status,
				]
			);

			return redirect()->route('backend_static_pages_index')->with('success', "{$validated['type']} '{$validated['title']}' saved successfully!");
		} catch (Exception $e) {
			return redirect()->back()->with('error', "OOPS!! {$validated['type']} '{$validated['title']}' failed to save! " . $e->getMessage());
		}
	}

	public function publicUrlAbout()
	{
		$static = OptionStaticPage::where('type', 'about_us')->first();

		return view('System.static_pages.about_us', compact('static'));
	}

	public function publicUrlTerms()
	{
		$static = OptionStaticPage::where('type', 'term_n_conditions')->first();

		return view('System.static_pages.terms', compact('static'));
	}

	public function publicUrlPrivacy()
	{
		$static = OptionStaticPage::where('type', 'privacy_policy')->first();

		return view('System.static_pages.privacy', compact('static'));
	}

	public function publicUrlHelpSupport()
	{
		$static = OptionStaticPage::where('type', 'help_n_support')->first();

		return view('System.static_pages.help', compact('static'));
	}
	// ---------------------- STATIC PAGES ----------------------


	// ---------------------- DATABASE SEEDER ----------------------

	public function dbSeeding()
	{
		return view('System.Settings.seeding');
	}
	// ---------------------- DATABASE SEEDER ----------------------


	// ---------------------- EMAIL MANAGEMENT ----------------------

	public function mailIndex()
	{
		$emailTemp = OptionEmailTemplate::all();
		return view('System.Settings.email_management', compact('emailTemp'));
	}

	public function mailStore(Request $request)
	{
		try {
			$validated = $request->validate([
				'flag' => 'required|string|max:255',   // flag
				'subject' => 'required|string|max:255',
				'greeting' => 'nullable|string',
				'body_message' => 'nullable|string',
				'end_message' => 'nullable|string',
				'support_message' => 'nullable|string',
				'support_details' => 'nullable|string',
			]);
			$mailTemplate = OptionEmailTemplate::updateOrCreate(
				[
					'flag' => $validated['flag']
				],
				[
					'subject' => $validated['subject'],
					'greeting' => $validated['greeting'] ?? null,
					'body_message' => $validated['body_message'] ?? null,
					'end_message' => $validated['end_message'] ?? null,
					'support_message' => $validated['support_message'] ?? null,
					'support_details' => $validated['support_details'] ?? null,
					'status' => 1,
				]
			);

			return redirect()->back()->with('success', 'Email template saved successfully.');

		} catch (Throwable $e) {
			Log::error('Mail template save failed', [
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine()
			]);

			return redirect()->back()->with('error', 'Something went wrong while saving the email template. ' . $e->getMessage());
		}
	}
	// ---------------------- EMAIL MANAGEMENT ----------------------


	// ---------------------- BASIC SITE SETUP (ICON+LOGO) ----------------------

	public function siteSetupIndex()
	{
		$siteSetupTitle = OptionSiteSetup::where('type', 'site_basic')->pluck('value', 'name')->toArray();
		return view('System.Settings.site_setup', compact('siteSetupTitle'));
	}

	public function siteSetupStore(Request $request)
	{
		try {
			$user = $request->user();

			if (!$request->hasFile('favicon') || $request->file('favicon')->getSize() === 0) {
				$request->request->remove('favicon');
				$request->files->remove('favicon');
			}

			$validated = $request->validate([
				'site_title' => 'nullable|string|max:255',
				'site_logo' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
				'logo_size' => 'nullable|string',
				'favicon' => 'nullable|file|max:128', // Simplified validation
				'remove_image' => 'nullable|boolean',
			]);

			$oldLogo = OptionSiteSetup::where(['type' => 'site_basic', 'name' => 'site_logo',])->value('value');
			$oldFavicon = OptionSiteSetup::where(['type' => 'site_basic', 'name' => 'favicon',])->value('value');

			// ------------------ FILE HANDLING ------------------
			$REMOVE_LOGO = $request->boolean('remove_image');
			$site_logo_path = $oldLogo;
			$favicon_path = $oldFavicon;
			// dd($request->site_logo, $request->favicon);
			if ($request->hasFile('site_logo')) {
				$site_logo_path = FileManagement::handleDirectPublicAsset(
					$request->file('site_logo'),
					'site_logo',
					$oldLogo,
					'logo_n_icons',
					false
				);
			} elseif ($REMOVE_LOGO) {
				$site_logo_path = FileManagement::handleDirectPublicAsset(
					null,
					'site_logo',
					$oldLogo,
					'logo_n_icons',
					true
				);
			}

			// Handle favicon
			if ($request->hasFile('favicon')) {
				$favicon_path = FileManagement::handleDirectPublicAsset(
					$request->file('favicon'),
					'favicon',
					$oldFavicon,
					null,
					false
				);
			}
			// ------------------ FILE HANDLING ------------------

			$data = [
				'site_title' => $validated['site_title'] ?? null,
				'site_logo' => $site_logo_path,
				'logo_size' => $validated['logo_size'] ?? "w-full",
				'favicon' => $favicon_path,
			];

			foreach ($data as $key => $value) {
				OptionSiteSetup::updateOrCreate(
					[
						'type' => 'site_basic',
						'name' => $key,
					],
					[
						'value' => $value,
						'log' => 'Last updated by: ' . ($user->email ?? 'System'),
						'status' => 1,
					]
				);
			}

			Cache::forget('site:basic');
			return redirect()->back()->with('success', 'Site details saved successfully. Reload the page to see them in effect.');

		} catch (Throwable $e) {
			Log::error('Site details save failed', ['message' => $e->getMessage(),]);
			return redirect()->back()->with('error', 'Something went wrong while saving the site setups. ' . $e->getMessage());
		}
	}
	// ---------------------- BASIC SITE SETUP (ICON+LOGO) ----------------------


	// ---------------------- SYSTEM::DEFAULT+MAILER ----------------------

	public function systemIndex()
	{
		$emailEnvVars = OptionEnv::where('var_type', 'mail')->pluck('value', 'name')->toArray();
		$defaultEnvVars = OptionEnv::where('var_type', 'default')->pluck('value', 'name')->toArray();

		return view('System.Settings.system_management', compact('emailEnvVars', 'defaultEnvVars'));
	}

	public function default(Request $request)
	{
		$validated = $request->validate([
			'timezone' => 'required|string',
		]);

		// dd($validated);
		try {
			$keys = [
				'DISPLAY_TIMEZONE' => $validated['timezone'],
			];

			foreach ($keys as $key => $value) {
				OptionEnv::updateOrCreate(
					[
						'var_type' => 'default',
						'name' => $key,
					],
					[
						'value' => $value,
						'status' => 1,
					]
				);
			}

			Cache::forget('env:default');

			return back()->with('success', 'Default settings updated successfully!');
		} catch (Exception $e) {
			return back()->with('error', 'Failed to update default system settings: ' . $e->getMessage());
		}
	}

	public function mailerStore(Request $request)
	{
		$request->validate([
			'mail_mailer' => 'required|string',
			'mail_host' => 'required|string',
			'mail_port' => 'required|string',
			'mail_username' => 'required|string',
			'mail_password' => 'required|string',
			'mail_encryption' => 'required|string',
			'mail_scheme' => 'nullable|string',
			'mail_from_name' => 'required|string',
			'mail_from_address' => 'required|email',
		]);

		try {
			// Define all mail keys
			$keys = [
				'MAIL_MAILER',
				'MAIL_HOST',
				'MAIL_PORT',
				'MAIL_USERNAME',
				'MAIL_PASSWORD',
				'MAIL_ENCRYPTION',
				'MAIL_SCHEME',
				'MAIL_FROM_NAME',
				'MAIL_FROM_ADDRESS'
			];

			foreach ($keys as $key) {
				OptionEnv::updateOrCreate(
					[
						'var_type' => 'mail',
						'name' => $key,
					],
					[
						'value' => $request->input(strtolower($key)),
						'status' => 1,
					]
				);
			}

			Cache::forget('env:mail');

			return back()->with('success', 'Mail settings updated successfully!');
		} catch (Exception $e) {
			return back()->with('error', 'Failed to update mail settings: ' . $e->getMessage());
		}
	}
	// ---------------------- SYSTEM::DEFAULT+MAILER ----------------------


	// ---------------------- NOTIFICATION-MANAGEMENT ----------------------

	public function notificationIndex()
	{
		$notificationSetup = OptionSiteSetup::where('type', 'notifications')->pluck('value', 'name')->toArray();

		return view('System.Settings.notification_management', compact('notificationSetup'));
	}

	public function notificationStore(Request $request)
	{
		$validated = $request->validate([
			'in_app_notification' => 'nullable|boolean',
			'email_notification' => 'nullable|boolean',
			'push_notification' => 'nullable|boolean',
			'multi_push_notification' => 'nullable|boolean',
			'admin_log' => 'nullable|boolean',
			'user_log' => 'nullable|boolean',
		]);

		try {
			$data = [
				'in_app_notification' => (int)($validated['in_app_notification'] ?? 1),
				'email_notification' => (int)($validated['email_notification'] ?? 1),
				'push_notification' => (int)($validated['push_notification'] ?? 1),
				'multi_push_notification' => (int)($validated['multi_push_notification'] ?? 1),
				'admin_log' => (int)($validated['admin_log'] ?? 1),
				'user_log' => (int)($validated['user_log'] ?? 1),
			];

			foreach ($data as $key => $value) {
				$updateData = [
					'value' => $value,
					'status' => 1,
				];

				if ($value == 1) {
					$updateData['log'] = 'Last updated by: ' . (Auth::user()->email ?? 'System');
				}

				OptionSiteSetup::updateOrCreate(
					[
						'type' => 'notifications',
						'name' => $key,
					],
					$updateData
				);
			}

			Artisan::call('config:clear');
			Artisan::call('cache:clear');

			return back()->with('success', 'Notification settings updated successfully!');
		} catch (Exception $e) {
			return back()->with('error', 'Failed to update notifications settings: ' . $e->getMessage());
		}
	}
	// ---------------------- NOTIFICATION-MANAGEMENT ----------------------


	// ---------------------- PAYMENT:KEYS ----------------------

	public function pricingKeyIndex()
	{
		$stripeKeys = OptionEnv::varType('stripe')->get()->pluck('value', 'name')->toArray();
		$revenueCatKeys = OptionEnv::varType('revenuecat')->get()->pluck('value', 'name')->toArray();

		$currencies = CurrencyEnums::cases();

		return view('System.Settings.pricing_key_management', compact('stripeKeys', 'revenueCatKeys', 'currencies'));
	}

	public function pricingWebKeyStore(Request $request)
	{
		try {
			$validated = $request->validate([
				'stripe_key' => 'nullable|string|starts_with:pk_',
				'secret_key' => 'nullable|string|starts_with:sk_',
				'webhook_secret' => 'nullable|string|starts_with:whsec_',
				'stripe_currency' => ['nullable', 'string', Rule::enum(CurrencyEnums::class)],
				'clear_keys' => 'nullable|boolean'
			]);

			// Step 1: Temporarily clear Stripe’s current key (from provider)
			Stripe::setApiKey(null);

			// Step 2: Test the new secret key directly
			if (!empty($validated['secret_key'])) {
				Stripe::setApiKey($validated['secret_key']);
				\Stripe\Account::retrieve(); // Throws if invalid
			}
			// ----------------------------

			// Step 3: Existing keys
			$optionKeys = OptionEnv::varType('stripe');

			if (!empty($validated['clear_keys'])) {
				if ($optionKeys) {
					$optionKeys->delete();
				}
				return back()->with('success', 'Stripe keys were flushed successfully.');
			}
			$existingKeys = $optionKeys ? $optionKeys->pluck('value', 'name')->toArray() : [];

			$keys = [
				'STRIPE_KEY' => $validated['stripe_key'] ?? null,
				'STRIPE_SECRET' => $validated['secret_key'] ?? null,
				'STRIPE_WEBHOOK_SECRET' => $validated['webhook_secret'] ?? null,
				'STRIPE_CURRENCY' => $validated['stripe_currency'] ?? null,
			];

			foreach ($keys as $key => $value) {

				$option = OptionEnv::firstOrNew([
					'var_type' => 'stripe',
					'name' => $key
				]);

				$option->needEncrypt = $key !== 'STRIPE_CURRENCY';  // must be first
				$option->value = $value !== null ? $value : ($existingKeys[$key] ?? null);
				$option->status = true;
				$option->save();
			}
			// ----------------------------

			// Step 4: Clear config + cache so provider reloads next request
			Artisan::call('config:clear');
			Artisan::call('cache:clear');

			cache()->forget('active_stripe_keys');

			cache()->put('active_stripe_keys', OptionEnv::where('var_type', 'stripe')->get()->pluck('value', 'name')->toArray(), 300);

			return back()->with('success', 'Stripe keys were validated and saved successfully. Changes will apply on next request.');

		} catch (\Stripe\Exception\AuthenticationException $e) {
			return back()->with('error', 'Invalid Stripe keys. Please check and try again.')->withInput();
		} catch (Exception $e) {
			Log::error('Stripe key update failed', ['error' => $e->getMessage()]);
			return back()->with('error', 'Server error: ' . $e->getMessage())->withInput();
		}
	}

	public function pricingMobileKeyStore(Request $request)
	{
		try {
			$validated = $request->validate([
				'rvc_public_key' => 'nullable|string',
				'rvc_secret_key' => 'nullable|string',
				'rvc_auth_token' => 'nullable|string',
			]);
			// ----------------------------
			$keys = [
				'REVENUECAT_PUBLIC_API_KEY' => $validated['rvc_public_key'] ?? null,
				'REVENUECAT_SECRET_KEY' => $validated['rvc_secret_key'] ?? null,
				'REVENUECAT_AUTH_TOKEN' => $validated['rvc_auth_token'] ?? null,
			];

			foreach ($keys as $key => $value) {
				OptionEnv::updateOrCreate(
					[
						'var_type' => 'revenuecat',
						'name' => $key,
					],
					[
						'needEncrypt' => 1,
						'value' => $value,
						'status' => 1,
					]
				);
			}
			// ----------------------------
			Artisan::call('config:clear');
			Artisan::call('cache:clear');
			cache()->forget('active_rvc_keys');

			return back()->with('success', 'RevenueCat keys were validated and saved successfully. Changes will apply on next request.');

		} catch (Exception $e) {
			Log::error('RevenueCat key update failed', ['error' => $e->getMessage()]);
			return back()->with('error', 'Server error: ' . $e->getMessage())->withInput();
		}
	}
	// ---------------------- PAYMENT:KEYS ----------------------


	// ---------------------- AI-MANAGEMENT ----------------------

	public function aiIndex()
	{
		$aiKey = OptionEnv::where('var_type', 'ai')->pluck('value', 'name')->toArray();
		$aiSetup = OptionSiteSetup::where('type', 'open_ai')->pluck('value', 'name')->toArray();

		return view('System.Settings.ai_management', compact('aiKey', 'aiSetup'));
	}

	public function aiStore(Request $request)
	{
		$validated = $request->validate([
			'OPENAI_API_KEY' => 'nullable|string',
			'ai_model' => 'nullable|string',
			'max_output_tokens' => 'nullable|integer',
			'timeout_seconds' => 'nullable|integer',
			'api_endpoint' => 'nullable|string',
			'role_system_content' => 'nullable|string|max:1000',
		]);

		try {
			$aiSettings = [
				'ai_model' => $validated['ai_model'] ?? 'gpt-4.1-mini',
				'max_output_tokens' => $validated['max_output_tokens'] ?? 9999,
				'timeout_seconds' => $validated['timeout_seconds'] ?? 120,
				'api_endpoint' => $validated['api_endpoint'] ?? 'https://api.openai.com/v1/responses',
				'role_system_content' => $validated['role_system_content']
					?? 'You are a gym & dietry trainer, skilled in training in complex situations. You consult with clients on their exercises and diet plan to help them get fit.',
			];

			foreach ($aiSettings as $key => $value) {

				OptionSiteSetup::updateOrCreate(
					[
						'type' => 'open_ai',
						'name' => $key,
					],
					[
						'value' => $value,
						'status' => 1,
						'log' => 'Last updated by: ' . (Auth::user()->email ?? 'System'),
					]
				);
			}

			$key = OptionEnv::updateOrCreate(
				[
					'var_type' => 'ai',
					'name' => 'OPENAI_API_KEY',
				],
				[
					'needEncrypt' => (int)1,
					'status' => 1
				]
			);
			if (isset($validated['OPENAI_API_KEY']) && !empty($validated['OPENAI_API_KEY'])) {
				$key->value = $validated['OPENAI_API_KEY'] === 'cancel' ? null : $validated['OPENAI_API_KEY'];
				$key->save();
			}

			Artisan::call('config:clear');
			Artisan::call('cache:clear');
			Cache::forget('env:openai');

			return back()->with('success', 'AI settings updated successfully!');
		} catch (Exception $e) {
			return back()->with('error', 'Failed to update AI settings: ' . $e->getMessage());
		}
	}
	// ---------------------- AI-MANAGEMENT ----------------------
}
