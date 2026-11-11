<?php

namespace App\Http\Controllers\System\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\{Artisan, Auth, Cache, DB, Log, Storage};
use Throwable;
use Exception;

class WebSPanelController extends Controller
{
	/*
		This is for S-Panel only for super admins so that they don't get locked out.
	*/
	private string $ADMIN_SECURITY_KEY;

	public function __construct()
	{
		$this->ADMIN_SECURITY_KEY = config('services.artisan.spanel_secret');
	}

	public function index()
	{
		return view('System.Settings.s_panel');
	}

	public function run(Request $request)
	{
		try {
			$validated = $request->validate([
				'dev_key' => 'required|string|min:1',
				'command' => 'required|string|min:8',
			]);
			// dd($validated, $this->ADMIN_SECURITY_KEY);

			if ($validated['dev_key'] !== $this->ADMIN_SECURITY_KEY) {
				abort(403, "Your security code doesn't match. Unauthorized!");
			}

			DB::beginTransaction();
			Artisan::call($validated['command']);

			$output = Artisan::output();
			DB::commit();
			$output = str_replace(["\r\n", "\r"], "\n", $output);

			return redirect()->back()->with('dpanel', "Command ran successfully!\n\n" . $output);

		} catch (Throwable $e) {
			if (app()->environment('local'))
				Log::error('Command failed to run', ['error' => $e->getMessage()]);
			return back()->with('error', "Command failed to run!...");
		}
	}

	// ---------------------- PURGING ----------------------
}
