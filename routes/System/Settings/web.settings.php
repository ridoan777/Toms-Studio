<?php

use App\Http\Controllers\System\Settings\{WebDPanelController,WebSPanelController};
use App\Http\Controllers\System\Settings\{WebArtisanController,WebProfileController,WebSettingsController};
use Illuminate\Support\Facades\Route;

# this route will only contain navbar settings (except for profile routes that comes default on web.php)


// ----------------- USER::ADMIN PROFILE -----------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [WebProfileController::class, 'edit'])->name('Mastering_Profile_edit');
    Route::patch('/profile', [WebProfileController::class, 'update'])->name('profile_update');
    Route::delete('/profile', [WebProfileController::class, 'destroy'])->name('profile_destroy');
});
// ----------------- USER::ADMIN PROFILE -----------------


// ----------------- DEV-PANEL -----------------

Route::middleware(['auth:web'])->group(function () {
    
    Route::get('/d-panel', [WebDPanelController::class, 'index'])->name('backend_d_panel_index');

    Route::post('/d-panel/store', [WebDPanelController::class, 'run'])->name('backend_d_panel_run');

});
// ----------------- DEV-PANEL -----------------


// ----------------- SUPER ADMIN-PANEL -----------------

Route::middleware(['auth:web', 'verify_role_key:super_admin'])->group(function () {
    
    Route::get('/super-admin/s-panel', [WebSPanelController::class, 'index'])->name('backend_s_panel_index');

    Route::post('/super-admin/s-panel/store', [WebSPanelController::class, 'run'])->name('backend_s_panel_run');

});
// ----------------- SUPER ADMIN-PANEL -----------------


// ----------------- SETTINGS -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/index', [WebSettingsController::class, 'index'])->name('backend_settings_index');

});
// ----------------- SETTINGS -----------------


// ----------------- SETTINGS::PURGING -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/purging', [WebSettingsController::class, 'indexPurging'])->name('backend_settings_purging_index');
    
    Route::delete('/settings/purging/unapproved', [WebSettingsController::class, 'purgingUnapproved'])->name('backend_settings_purging_unapproved');
    
    Route::delete('/settings/purging/disk-cleanup', [WebSettingsController::class, 'purgingDiskCleanup'])->name('backend_settings_purging_discleanup');

});
// ----------------- SETTINGS::PURGING -----------------



// ------------ STATIC PAGES [ABOUT, TERMS, PRIVACY, HELPS] ------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
	
	Route::get('/settings/static-pages', [WebSettingsController::class, 'indexStaticPages'])->name('backend_static_pages_index');
	
	Route::post('/settings/about-us/store', [WebSettingsController::class, 'storeStaticPages'])->name('backend_static_page_store');
    
});

Route::prefix('/')->group(function () {
	
    Route::get('/about-us', [WebSettingsController::class, 'publicUrlAbout'])->name('public_url_about');
	
    Route::get('/terms-and-condition', [WebSettingsController::class, 'publicUrlTerms'])->name('public_url_terms');
	
    Route::get('/privacy-policy', [WebSettingsController::class, 'publicUrlPrivacy'])->name('public_url_privacy');
	
    Route::get('/help-and-support', [WebSettingsController::class, 'publicUrlHelpSupport'])->name('public_url_help_support');
    
});
// ------------ STATIC PAGES [ABOUT, TERMS, PRIVACY] ------------



// ----------------- SETTINGS::DATABASE-SEEDING -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/seeding', [WebSettingsController::class, 'dbSeeding'])->name('backend_settings_seeding');
    
    Route::get('/settings/seeding/{page}', [WebArtisanController::class, 'adminArtianSeeder'])->name('backend_url_direct_seeding');
    
});
Route::get('/settings/seeding/{secret}/{page}', [WebArtisanController::class, 'devArtianSeeder']);

// ----------------- SETTINGS::DATABASE-SEEDING -----------------



// ----------------- SETTINGS::EMAIL-MANAGEMENT -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/mail-management', [WebSettingsController::class, 'mailIndex'])->name('backend_settings_mail_index');

    Route::post('/settings/mail-management', [WebSettingsController::class, 'mailStore'])->name('backend_settings_mail_store');


});
// ----------------- SETTINGS::EMAIL-MANAGEMENT -----------------


// ----------------- SETTINGS::SITE-MANAGEMENT -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/site-management', [WebSettingsController::class, 'siteSetupIndex'])->name('backend_settings_site_setup_index');

    Route::post('/settings/site-management', [WebSettingsController::class, 'siteSetupStore'])->name('backend_settings_site_setup_store');
});
// ----------------- SETTINGS::SITE-MANAGEMENT -----------------



// ----------------- SETTINGS::NOTIFICATION-MANAGEMENT -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/notification-management', [WebSettingsController::class, 'notificationIndex'])->name('backend_settings_notification_index');

    Route::post('/settings/notification-management/store', [WebSettingsController::class, 'notificationStore'])->name('backend_settings_notification_store');
});
// ----------------- SETTINGS::NOTIFICATION-MANAGEMENT -----------------



// ----------------- SETTINGS::PRICING -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/pricing/keys/index', [WebSettingsController::class, 'pricingKeyIndex'])->name('backend_settings_pricing_key_index');

    Route::post('/settings/web/pricing/keys/index', [WebSettingsController::class, 'pricingWebKeyStore'])->name('backend_settings_web_pricing_key_store');

    Route::post('/settings/mobile/pricing/keys/index', [WebSettingsController::class, 'pricingMobileKeyStore'])->name('backend_settings_mobile_pricing_key_store');
});
// ----------------- SETTINGS::PRICING -----------------



// ----------------- SETTINGS::AI -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/ai', [WebSettingsController::class, 'aiIndex'])->name('backend_settings_ai_index');

    Route::post('/settings/ai/store', [WebSettingsController::class, 'aiStore'])->name('backend_settings_ai_store');
});
// ----------------- SETTINGS::AI -----------------


// ----------------- SETTINGS::SYSTEM-MANAGEMENT -----------------

Route::prefix('admin')->middleware(['auth:web'])->group(function () {
    
    Route::get('/settings/system-management', [WebSettingsController::class, 'systemIndex'])->name('backend_settings_system_index');

    Route::post('/settings/system-management/defaults', [WebSettingsController::class, 'default'])->name('backend_settings_system_default_store');

    Route::post('/settings/system-management/mailer', [WebSettingsController::class, 'mailerStore'])->name('backend_settings_system_mailer_store');


});
// ----------------- SETTINGS::SYSTEM-MANAGEMENT -----------------