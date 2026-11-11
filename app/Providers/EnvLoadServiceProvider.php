<?php

namespace App\Providers;

use App\Models\System\Settings\OptionEnv;
use Illuminate\Support\Facades\{Cache, Config, Log, Schema};
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
use Throwable;

class EnvLoadServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		// 
	}

	public function boot(): void
	{
		if (!Cache::has('db:env_ready')) {
			try {
				$exists = Schema::hasTable('option_envs');

				Cache::put('db:env_ready', $exists, now()->addHours(6));
			} catch (Throwable $e) {
				Log::error("EnvLoadServiceProvider schema check failed", [$e->getMessage()]);
				return;
			}
		}

		if (!Cache::get('db:env_ready')) {
			return;
		}

		// ------------------------- 1. MAIL CONFIGURATION -------------------------
		$mailSettings = Cache::remember('env:mail', 300, function () {
			return OptionEnv::where('var_type', 'mail')->where('status', 1)->pluck('value', 'name')->toArray();
		});
		if (!empty($mailSettings)) {
			Config::set([
				'mail.default' => $mailSettings['MAIL_MAILER'] ?? config('mail.default'),
				'mail.mailers.smtp.transport' => 'smtp',
				'mail.mailers.smtp.scheme' => $mailSettings['MAIL_SCHEME'] ?? config('mail.mailers.smtp.scheme'),
				'mail.mailers.smtp.host' => $mailSettings['MAIL_HOST'] ?? config('mail.mailers.smtp.host'),
				'mail.mailers.smtp.port' => (int)($mailSettings['MAIL_PORT'] ?? config('mail.mailers.smtp.port')),
				'mail.mailers.smtp.username' => $mailSettings['MAIL_USERNAME'] ?? config('mail.mailers.smtp.username'),
				'mail.mailers.smtp.password' => $mailSettings['MAIL_PASSWORD'] ?? config('mail.mailers.smtp.password'),
				'mail.mailers.smtp.encryption' => $mailSettings['MAIL_ENCRYPTION'] ?? config('mail.mailers.smtp.encryption'),
				'mail.from.address' => $mailSettings['MAIL_FROM_ADDRESS'] ?? config('mail.from.address'),
				'mail.from.name' => $mailSettings['MAIL_FROM_NAME'] ?? config('mail.from.name'),
			]);
		}
		// ------------------------- 1. MAIL CONFIGURATION -------------------------


		// ------------------------- 2. STRIPE KEYS CONFIGURATION -------------------------
		$stripeKeys = Cache::remember('active_stripe_keys', 300, function () {
			return OptionEnv::where('var_type', 'stripe')->get()->pluck('value', 'name')->toArray();
		});

		// $stripeKeys = Cache::rememberForever('active_stripe_keys', function () {
		// 	return OptionEnv::where('var_type', 'stripe')->get()->pluck('value', 'name')->toArray();
		// });

		if (!empty($stripeKeys)) {
			Config::set([
				'services.stripe.key' => $stripeKeys['STRIPE_KEY'] ?? null,
				'services.stripe.secret' => $stripeKeys['STRIPE_SECRET'] ?? null,
				'services.stripe.webhook_secret' => $stripeKeys['STRIPE_WEBHOOK_SECRET'] ?? null,
				'services.stripe.stripe_currency' => $stripeKeys['STRIPE_CURRENCY'] ?? null,

				// If we're using Cashier:
				'cashier.key' => $stripeKeys['STRIPE_KEY'] ?? null,
				'cashier.secret' => $stripeKeys['STRIPE_SECRET'] ?? null,
				'cashier.webhook.secret' => $stripeKeys['STRIPE_WEBHOOK_SECRET'] ?? null,
				'cashier.webhook.stripe_currency' => $stripeKeys['STRIPE_CURRENCY'] ?? null,
			]);

			// Global Stripe key
			if (!empty($stripeKeys['STRIPE_SECRET'])) {
				Stripe::setApiKey($stripeKeys['STRIPE_SECRET']);
			}
		}
		// ------------------------- 2. STRIPE KEYS CONFIGURATION -------------------------


		// ------------------------- 3. REVENUECAT KEYS CONFIGURATION -------------------------
		$revenueCatKeys = Cache::remember('env:active_rvc_keys', 300, function () {
			return OptionEnv::where('var_type', 'revenuecat')->get()->pluck('value', 'name')->toArray();
		});

		if (!empty($revenueCatKeys)) {
			Config::set([
				'services.revenuecat.key' => $revenueCatKeys['REVENUECAT_PUBLIC_API_KEY'] ?? null,
				'services.revenuecat.secret' => $revenueCatKeys['REVENUECAT_SECRET_KEY_'] ?? null,
				'services.revenuecat.webhook_secret' => $revenueCatKeys['REVENUECAT_AUTH_TOKEN'] ?? null,
			]);
		}
		// ------------------------- 3. REVENUECAT KEYS CONFIGURATION -------------------------


		// ------------------------- 4. OPEN AI KEYS -------------------------
		$AIKey = Cache::remember('env:openai', 300, function () {
			return OptionEnv::where('var_type', 'ai')->get()->pluck('value', 'name')->toArray();
		});

		if (!empty($AIKey)) {
			Config::set([
				'services.openai.chatgpt' => $AIKey['OPENAI_API_KEY'] ?? null,
			]);
		}
		// ------------------------- 4. OPEN AI KEYS -------------------------


		// ------------------------- 5. SYSTEM DEFAULTS -------------------------
		$displayTimezone = Cache::remember('env:default', 300, function () {
			return OptionEnv::where('var_type', 'default')->where('name', 'DISPLAY_TIMEZONE')->value('value');
		});

		if ($displayTimezone) {
			Config::set('settings.display_timezone', $displayTimezone);
		}
		// ------------------------- 5. SYSTEM DEFAULTS -------------------------

	}
}
