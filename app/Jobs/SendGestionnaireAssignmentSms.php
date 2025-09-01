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

class SendGestionnaireAssignmentSms implements ShouldQueue
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
            if (!$this->sinistre->assure_id || !$this->sinistre->gestionnaire_id) {
                Log::warning('Sinistre sans assuré ou gestionnaire pour SMS d\'affectation', [
                    'sinistre_id' => $this->sinistre->id,
                    'numero_sinistre' => $this->sinistre->numero_sinistre,
                    'assure_id' => $this->sinistre->assure_id,
                    'gestionnaire_id' => $this->sinistre->gestionnaire_id
                ]);
                return;
            }

            $this->sinistre->load(['assure', 'gestionnaire']);

            $assure = $this->sinistre->assure;
            $gestionnaire = $this->sinistre->gestionnaire;

            $telephoneAssure = $this->sinistre->telephone_assure ?? $assure->telephone ?? null;
            
            if (empty($telephoneAssure)) {
                Log::warning('Numéro de téléphone manquant pour SMS d\'affectation de gestionnaire', [
                    'sinistre_id' => $this->sinistre->id,
                    'numero_sinistre' => $this->sinistre->numero_sinistre,
                    'assure_id' => $assure->id
                ]);
                return;
            }

            $nomAssure = strtoupper(explode(' ', trim($assure->nom_complet ?? $this->sinistre->nom_assure))[0]);
            $nomGestionnaire = $gestionnaire->nom_complet ?? 'Gestionnaire';
            $statutLibelle = $this->sinistre->statut_libelle;
            
            $message = "SAAR ASSURANCE\nCher(e) {$nomAssure}, votre sinistre N°{$this->sinistre->numero_sinistre} a ete affecte a {$nomGestionnaire}. Statut: {$statutLibelle}. Vous serez contacte(e) prochainement.";

            if (strlen($message) > 160) {
                $message = "SAAR ASSURANCE\nSinistre N°{$this->sinistre->numero_sinistre} affecte a un gestionnaire. Statut: {$statutLibelle}. Vous serez contacte prochainement.";
            }

            $result = $orangeService->sendSMS(
                $telephoneAssure,
                $message,
                'SAAR CI'
            );

            Log::info('SMS d\'affectation de gestionnaire envoyé avec succès', [
                'sinistre_id' => $this->sinistre->id,
                'numero_sinistre' => $this->sinistre->numero_sinistre,
                'telephone_assure' => $telephoneAssure,
                'gestionnaire_nom' => $nomGestionnaire,
                'statut' => $this->sinistre->statut,
                'message_length' => strlen($message),
                'response' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS d\'affectation de gestionnaire', [
                'sinistre_id' => $this->sinistre->id,
                'numero_sinistre' => $this->sinistre->numero_sinistre,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
