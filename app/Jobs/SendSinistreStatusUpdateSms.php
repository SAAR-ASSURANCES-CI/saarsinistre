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

class SendSinistreStatusUpdateSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Sinistre $sinistre,
        public string $ancienStatut
    ) {}

    /**
     * Execute the job.
     */
    public function handle(OrangeService $orangeService): void
    {
        try {
            if (!$this->sinistre->assure_id) {
                Log::warning('Sinistre sans assuré pour SMS de changement de statut', [
                    'sinistre_id' => $this->sinistre->id,
                    'numero_sinistre' => $this->sinistre->numero_sinistre
                ]);
                return;
            }

            $this->sinistre->load(['assure', 'gestionnaire']);

            $assure = $this->sinistre->assure;

            $telephoneAssure = $this->sinistre->telephone_assure ?? $assure->telephone ?? null;
            
            if (empty($telephoneAssure)) {
                Log::warning('Numéro de téléphone manquant pour SMS de changement de statut', [
                    'sinistre_id' => $this->sinistre->id,
                    'numero_sinistre' => $this->sinistre->numero_sinistre,
                    'assure_id' => $assure->id
                ]);
                return;
            }

            $statutsNotifiables = ['en_cours', 'expertise_requise', 'pret_reglement', 'regle', 'refuse', 'clos'];
            
            if (!in_array($this->sinistre->statut, $statutsNotifiables)) {
                Log::info('Statut non notifiable par SMS', [
                    'sinistre_id' => $this->sinistre->id,
                    'statut' => $this->sinistre->statut
                ]);
                return;
            }

            $nomAssure = strtoupper(explode(' ', trim($assure->nom_complet ?? $this->sinistre->nom_assure))[0]);
            $statutLibelle = $this->sinistre->statut_libelle;
            
            $message = "SAAR ASSURANCE\nCher(e) {$nomAssure}, le statut de votre sinistre N°{$this->sinistre->numero_sinistre} a ete mis a jour: {$statutLibelle}.";

            switch ($this->sinistre->statut) {
                case 'expertise_requise':
                    $message .= " Un expert va vous contacter prochainement.";
                    break;
                case 'pret_reglement':
                    $message .= " Votre dossier est pret pour le reglement.";
                    break;
                case 'regle':
                    $message .= " Votre sinistre a ete regle. Merci de votre confiance.";
                    break;
                case 'refuse':
                    $message .= " Veuillez contacter votre gestionnaire pour plus d'informations.";
                    break;
                case 'clos':
                    $message .= " Votre dossier est maintenant clos.";
                    break;
                default:
                    $message .= " Vous serez contacte(e) si necessaire.";
                    break;
            }

            // Vérifier la longueur du message et le raccourcir si nécessaire
            if (strlen($message) > 160) {
                $message = "SAAR ASSURANCE\nSinistre N°{$this->sinistre->numero_sinistre}: {$statutLibelle}. Contactez votre gestionnaire si necessaire.";
            }

            // Envoyer le SMS
            $result = $orangeService->sendSMS(
                $telephoneAssure,
                $message,
                'SAAR CI'
            );

            Log::info('SMS de changement de statut envoyé avec succès', [
                'sinistre_id' => $this->sinistre->id,
                'numero_sinistre' => $this->sinistre->numero_sinistre,
                'telephone_assure' => $telephoneAssure,
                'ancien_statut' => $this->ancienStatut,
                'nouveau_statut' => $this->sinistre->statut,
                'message_length' => strlen($message),
                'response' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de changement de statut', [
                'sinistre_id' => $this->sinistre->id,
                'numero_sinistre' => $this->sinistre->numero_sinistre,
                'ancien_statut' => $this->ancienStatut,
                'nouveau_statut' => $this->sinistre->statut,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Relancer l'exception pour permettre les tentatives de retry
            throw $e;
        }
    }
}
