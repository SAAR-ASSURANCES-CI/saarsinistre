<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\OrangeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendAccountCreationSms implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public function __construct(
        public User $user,
        public string $telephone
    ) {}

    public function handle(OrangeService $orangeService): void
    {
        try {
            $nomFormate = strtoupper(explode(' ', trim($this->user->nom_complet ?: 'CLIENT'))[0]);
            
            $message = "SAAR ASSURANCE\n";
            $message .= "Cher(e) {$nomFormate}, votre espace client est pret :\n";
            $message .= "Identifiant: {$this->user->username}\n";
            $message .= "Code: {$this->user->password_temp}\n";
            $message .= "Valable 48h";

            $orangeService->sendSmsConfirmationSinistre(
                $this->telephone,
                $this->user->nom_complet ?: 'CLIENT',
                'COMPTE-' . $this->user->username,
                $message
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de connexion: ' . $e->getMessage());
        }
    }

}
