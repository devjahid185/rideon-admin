<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use App\Models\Module;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirestoreService::class, function ($app) {
            return new FirestoreService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return; // Stop executing further code if DB not available
        }

        app()->singleton('currentModule', function () {
            return Cache::remember('current_module_default', 6000, function () {
                return Module::where('default_module', '1')->first();
            });
        });

        try {
            $settings = Cache::rememberForever('general_settings', function () {
                return GeneralSetting::pluck('meta_value', 'meta_key')->toArray();
            });

            foreach ($settings as $key => $value) {
                Config::set("general.$key", $value);
            }
        } catch (\Exception $e) {
            // silently skip if settings cannot be loaded
        }
    }
}
