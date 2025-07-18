<?php

namespace App\Jobs;

use App\Models\Sinistre;
use App\Services\OrangeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSinistreConfirmationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Sinistre $sinistre
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OrangeService $orangeService): void
    {
        try {
            if (empty($this->sinistre->telephone_assure)) {
                Log::warning('Numéro de téléphone manquant pour le sinistre', [
                    'sinistre_id' => $this->sinistre->id,
                    'numero_sinistre' => $this->sinistre->numero_sinistre
                ]);
                return;
            }
            $orangeService->sendSmsConfirmationSinistre(
                $this->sinistre->telephone_assure,
                $this->sinistre->nom_assure,
                $this->sinistre->numero_sinistre
            );
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de confirmation', [
                'sinistre_id' => $this->sinistre->id,
                'numero_sinistre' => $this->sinistre->numero_sinistre,
                'error' => $e->getMessage()
            ]);
        }
    }
}
