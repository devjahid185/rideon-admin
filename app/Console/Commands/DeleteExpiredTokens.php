<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;

class DeleteExpiredTokens extends Command
{
    protected $signature = 'tokens:cleanup';

    protected $description = 'Delete expired Sanctum personal access tokens';

    public function handle()
    {
        $count = PersonalAccessToken::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted $count expired tokens.");
    }
}
