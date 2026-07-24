<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (file_exists(base_path('routes/installer.php'))) {
            $this->loadRoutesFrom(base_path('routes/installer.php'));
        }
        if (file_exists(base_path('routes/front.php'))) {
            $this->loadRoutesFrom(base_path('routes/front.php'));
        }
    }
}
