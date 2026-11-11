<?php

namespace App\Providers;

use App\Models\System\Settings\OptionSiteSetup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SiteSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Nothing needed here for now
    }

    public function boot(): void
    {
        // Check if the table exists
        if (!Schema::hasTable('option_site_setups')) {
            return;
        }

        // Cache site settings for performance
        $siteSettings = Cache::remember('site:basic', 300, function () {
            return OptionSiteSetup::where('type', 'site_basic')
                ->where('status', 1)
                ->pluck('value', 'name')
                ->toArray();
        });

        /*
        |--------------------------------------------------------------------------
        | APPLY SITE CONFIGURATION OVERRIDES
        |--------------------------------------------------------------------------
        */
        // Set dynamic site title
        Config::set('app.name', $siteSettings['site_title'] ?? config('app.name'));

        // Optional: set other settings if you store them
        // Config::set('site.logo', $siteSettings['site_logo'] ?? null);
    }
}
