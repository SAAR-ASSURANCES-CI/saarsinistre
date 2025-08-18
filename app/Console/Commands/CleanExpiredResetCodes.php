<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PasswordResetCode;
use Carbon\Carbon;

class CleanExpiredResetCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset-codes:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les codes de réinitialisation de mot de passe expirés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCodes = PasswordResetCode::where('expires_at', '<', Carbon::now())->get();
        
        if ($expiredCodes->isEmpty()) {
            $this->info('Aucun code expiré à nettoyer.');
            return 0;
        }

        $count = $expiredCodes->count();
        
        foreach ($expiredCodes as $code) {
            $code->delete();
        }

        $this->info("{$count} codes de réinitialisation expirés ont été supprimés avec succès.");
        
        return 0;
    }
}
