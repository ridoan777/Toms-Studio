<?php

namespace App\Http\Controllers\System\Settings;

use App\Helpers\Errors\ExceptionHandling;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Artisan, DB, Log};
use Throwable;

class WebArtisanController extends Controller
{
	// private $SECURITY_KEY = "1.0.1-master";
	private string $SECURITY_KEY;

	public function __construct()
	{
		$this->SECURITY_KEY = config('services.artisan.secret');
	}
	// http://127.0.0.1:8000/settings/seeding/1.0.1-master/static_page
	public function devArtianSeeder($SECRET, $PAGE)
	{
		try {
			// dd("dev");
			$seedOutput = $this->seeder($SECRET, $PAGE);

			$artisanOutput = "<pre>"
				. $seedOutput['output'] . "......\n" .
				"✔️ STATUS : true\n"
				. "📄 MESSAGE: Database seeded successfully\n"
				. "🔢 CODE   : 200\n"
				. "</pre>";
			if($seedOutput['flag']){
				return response($artisanOutput);
			}
			return response($seedOutput['output']);

		} catch (Throwable $e) {
			Log::error('Dev seeding failed', ['error' => $e->getMessage()]);
			return ExceptionHandling::handle('running database seeder', $e);
		}
	}

	public function adminArtianSeeder($PAGE)
	{
		try {
			$SECRET = $this->SECURITY_KEY;
			$seedOutput = $this->seeder($SECRET, $PAGE);
			$message = "Database seeded successfully! Command: {$PAGE}";
			$result = 'success';
			
			if(!$seedOutput['flag']){
				$result = 'error';
				$message = "Error while seeding database! {$seedOutput['output']}";
			}
			return back()->with($result, $message);

		} catch (Throwable $e) {
			Log::error('Admin seeding failed', ['error' => $e->getMessage()]);
			return back()->with('error', "Static Pages failed to seed!..." . $seedOutput);
		}
	}

	private function seeder($secret, $page)
	{
		try {
			if ($secret !== $this->SECURITY_KEY) {
				abort(403, "Your security code doesn't match. Unauthorized!");
			}
			$seederPage = [
				'static_pages' => "Database\\Seeders\\System\\SaticSiteSeeder",
				'user' => "Database\\Seeders\\UserSeeder",
			];
			if (!array_key_exists($page, $seederPage)) {
				return [
					'flag' => false,
					'output' => "Invalid command!"
				];
			}
			DB::beginTransaction();
			Artisan::call('db:seed', [
				'--class' => $seederPage[$page],
				'--force' => true,
			]);

			$output = Artisan::output();
			DB::commit();

			return [
				'flag' => true,
				'output' => $output
			];
		} catch (Throwable $e) {
			DB::rollBack();
			// throw $e;
			return [
				'flag' => false,
				'output' => throw $e
			];
		}
	}

}
