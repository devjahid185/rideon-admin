<?php

namespace App\Providers;

use App\Models\Module;
use Illuminate\Support\ServiceProvider;

class ModulesComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        view()->composer('admin', function ($view) {
            // Fetch all modules from the database
            $modules = Module::all();

            // Share the modules variable with the admin view
            $view->with('modules', $modules);
        });
    }

    public function register()
    {
        // ...
    }
}
