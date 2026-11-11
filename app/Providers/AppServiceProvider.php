<?php

namespace App\Providers;

use App\Models\System\Security\CustomPersonalAccessToken;
use App\Models\Users\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Cashier::useCustomerModel(User::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(CustomPersonalAccessToken::class);

        $this->loadMigrationsFrom([
            database_path('migrations'),
            database_path('migrations/Defaults'),
            // --------------- PACKAGES ---------------
            database_path('migrations/Packages'),
            database_path('migrations/Packages/laravel_cashier'),
            database_path('migrations/Packages/Ai'),
            // --------------- SETTINGS ---------------
            database_path('migrations/Settings'),
            database_path('migrations/Settings/Options'),
            database_path('migrations/Settings/Notifications'),
            database_path('migrations/Settings/Role_Managements'),
            // --------------- MANAGEMENTS ---------------
            database_path('migrations/Managements'),
            // --------------- COMMUNICATIONS ---------------
            database_path('migrations/Communications'),
            // --------------- WORKFLOWS ---------------
            database_path('migrations/Workflows/Projects'),
            database_path('migrations/Workflows/Instruments'),
            database_path('migrations/Workflows/Lessons'),

            // --------------- BILLING ---------------
            database_path('migrations/Billings/Payments'),

            // --------------- RELATIONSHIP ---------------
            database_path('migrations/Relationships'),
        ]);

        Blade::directive('datetime', function ($expression) {
            return "<?php
                \$args = [$expression];

                \$date = \$args[0] ?? null;
                \$format = \$args[1] ?? 'd M Y, h:i A';

                if(\$date){
                    \$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

                    echo Carbon\Carbon::parse(\$date)->timezone(\$zones['zone'])->format(\$format) .' (UTC'.\$zones['offset'].')';
                }else{
                    echo 'N/A';
                }
            ?>";
        });

        Gate::before(function ($user, string $ability) {
            static $isSuperAdmin = null;

            $isSuperAdmin ??= $user->hasRoleKey('SUPER_ADMIN');

            return $isSuperAdmin ? true : null;
        });
    }
}
