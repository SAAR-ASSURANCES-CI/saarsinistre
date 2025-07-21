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

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $telephone
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OrangeService $orangeService): void
    {
        try {
            $message = "SAAR ASSURANCE\n";
            $message .= "Votre espace client est prêt :\n";
            $message .= "Identifiant: {$this->user->username}\n";
            $message .= "Code: {$this->user->password_temp}\n";
            $message .= "Valable 48h\n";

            $orangeService->sendSmsConfirmationSinistre(
                $this->telephone,
                $this->user->nom_complet,
                $this->user->username
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de connexion: ' . $e->getMessage());
        }
    }
}
