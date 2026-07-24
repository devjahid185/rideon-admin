<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteLaravelLog extends Command
{
    protected $signature = 'log:clean';

    protected $description = 'Delete laravel.log file if older than 10 days';

    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            $lastModified = filemtime($logPath);
            $daysOld = (time() - $lastModified) / 60 / 60 / 24;

            if ($daysOld >= 10) {
                unlink($logPath);
                $this->info('laravel.log deleted.');
            } else {
                $this->info('laravel.log is not old enough.');
            }
        } else {
            $this->info('laravel.log does not exist.');
        }
    }
}
