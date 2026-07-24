<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use App\Models\Module;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use PDOException;

class ViewComposerServiceProvider extends ServiceProvider
{
    protected $settings;

    protected $modules;

    protected $currentModule;

    public function boot()
    {
        try {
            DB::connection()->getPdo();

            if (Schema::hasTable('general_settings')) {
                $this->settings = cache()->remember('cached_general_settings', now()->addHours(24), function () {
                    return GeneralSetting::whereIn('meta_key', [
                        'general_favicon',
                        'general_logo',
                        'general_name',
                        'general_description',
                        'general_default_currency',
                    ])->get()->keyBy('meta_key');
                });
            }

            if (Schema::hasTable('module')) {
                $this->modules = cache()->remember('cached_modules', now()->addHours(24), function () {
                    return Module::all();
                });

                $this->currentModule = cache()->remember('cached_current_module', now()->addHours(24), function () {
                    return Module::where('default_module', 1)->first();
                });
            }

            View::share([
                'faviconPath' => isset($this->settings['general_favicon']) ? '/storage/'.$this->settings['general_favicon']->meta_value : null,
                'logoPath' => isset($this->settings['general_logo']) ? '/storage/'.$this->settings['general_logo']->meta_value : null,
                'siteName' => isset($this->settings['general_name']) ? $this->settings['general_name']->meta_value : null,
                'tagLine' => isset($this->settings['general_description']) ? $this->settings['general_description']->meta_value : null,
                'general_default_currency' => $this->settings['general_default_currency'] ?? null,
                'modules' => $this->modules ?? collect(),
                'currentModule' => $this->currentModule,
            ]);

        } catch (PDOException $e) {
            \Log::error('Database connection failed: '.$e->getMessage());
        } catch (QueryException $e) {
            \Log::error('Query failed: '.$e->getMessage());
        }
    }

    public function register()
    {
        //
    }
}
