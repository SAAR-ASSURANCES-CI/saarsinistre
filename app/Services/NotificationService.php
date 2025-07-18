<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Sinistre;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendSinistreConfirmationSms;
use App\Jobs\SendSinistreNotificationEmail;

class NotificationService
{
    public function triggerSinistreNotifications(Sinistre $sinistre): void
    {
        $this->sendEmailToManagers($sinistre);
        $this->sendSmsToAssure($sinistre);
    }

    protected function sendEmailToManagers(Sinistre $sinistre): void
    {
        try {
            $gestionnaires = User::where('role', 'gestionnaire')
                ->where('actif', true)
                ->get();

            if ($gestionnaires->isEmpty()) {
                Log::warning('Aucun gestionnaire actif trouvé', [
                    'sinistre_id' => $sinistre->id,
                    'numero_sinistre' => $sinistre->numero_sinistre
                ]);
                return;
            }

            SendSinistreNotificationEmail::dispatch($sinistre, $gestionnaires)
                ->delay(now()->addSeconds(5));

            Log::info('Job d\'envoi d\'email planifié avec succès', [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'nb_gestionnaires' => $gestionnaires->count()
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des emails aux gestionnaires: ' . $e->getMessage(), [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendSmsToAssure(Sinistre $sinistre): void
    {
        try {
            SendSinistreConfirmationSms::dispatch($sinistre);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de confirmation', [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'error' => $e->getMessage()
            ]);
        }
    }
}
