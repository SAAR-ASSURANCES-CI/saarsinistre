<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use App\Services\OrangeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendChatMessageNotificationSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $assure;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message, User $assure)
    {
        $this->message = $message;
        $this->assure = $assure;
    }

    /**
     * Execute the job.
     */
    public function handle(OrangeService $orangeService): void
    {
        try {
            $sinistre = $this->message->sinistre;
            
            Log::info('DEBUG - SendChatMessageNotificationSms - Données du sinistre', [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'telephone_assure' => $sinistre->telephone_assure,
                'telephone_assure_is_empty' => empty($sinistre->telephone_assure),
                'telephone_assure_length' => strlen($sinistre->telephone_assure ?? ''),
                'telephone_assure_trimmed' => trim($sinistre->telephone_assure ?? ''),
                'all_sinistre_attributes' => $sinistre->toArray()
            ]);
            
            if (empty($sinistre->telephone_assure)) {
                Log::warning('Numéro de téléphone manquant pour SMS de notification de message', [
                    'assure_id' => $this->assure->id,
                    'message_id' => $this->message->id,
                    'sinistre_id' => $this->message->sinistre_id,
                    'note' => 'Le numéro de téléphone doit être renseigné dans le sinistre (telephone_assure)',
                    'telephone_assure_value' => $sinistre->telephone_assure
                ]);
                return;
            }

            $telephone = $sinistre->telephone_assure;
            
            $smsMessage = $this->generateSmsMessage();

            $result = $orangeService->sendSMS(
                $telephone,
                $smsMessage,
                'SAAR CI'
            );

            Log::info('SMS de notification de nouveau message envoyé avec succès', [
                'assure_id' => $this->assure->id,
                'assure_telephone' => $telephone,
                'message_id' => $this->message->id,
                'sinistre_id' => $this->message->sinistre_id,
                'sms_result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de notification de nouveau message', [
                'assure_id' => $this->assure->id,
                'message_id' => $this->message->id,
                'sinistre_id' => $this->message->sinistre_id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate the SMS message content
     */
    private function generateSmsMessage(): string
    {
        $sinistre = $this->message->sinistre;
        $gestionnaire = $this->message->sender;

        $message = "SAAR CI - Nouveau message reçu\n";
        $message .= "Sinistre: {$sinistre->numero_sinistre}\n";
        $message .= "De: {$gestionnaire->nom_complet}\n";
        
        if (!empty($this->message->contenu)) {
            $extrait = strlen($this->message->contenu) > 50 
                ? substr($this->message->contenu, 0, 50) . '...'
                : $this->message->contenu;
            $message .= "Message: {$extrait}\n";
        }
        
        $message .= "Connectez-vous pour répondre.";

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        Log::error('Job d\'envoi de SMS de notification de nouveau message échoué', [
            'assure_id' => $this->assure->id,
            'message_id' => $this->message->id,
            'sinistre_id' => $this->message->sinistre_id,
            'error' => $exception->getMessage()
        ]);
    }
}
