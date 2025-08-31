<?php

namespace App\Jobs;

use Exception;
use App\Models\Sinistre;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSinistreNotificationEmail implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $sinistre;
    protected $gestionnaires;

    /**
     * Create a new job instance.
     */
    public function __construct(Sinistre $sinistre, $gestionnaires)
    {
        $this->sinistre = $sinistre;
        $this->gestionnaires = $gestionnaires;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $dataEmail = [
                'sinistre' => $this->sinistre,
                'url_sinistre' => route('dashboard'),
                'company' => [
                    'name' => 'SAAR ASSURANCE',
                    'phone' => '+225 20 30 30 30',
                    'email' => 'contact@saar-assurance.ci',
                    'address' => 'Abidjan, Côte d\'Ivoire'
                ]
            ];
            if (is_string($this->gestionnaires)) {
                try {
                    Mail::send('emails.nouveau-sinistre', $dataEmail, function ($message) {
                        $message->to($this->gestionnaires, 'Service Sinistres')
                            ->subject('Nouveau sinistre déclaré - N° ' . $this->sinistre->numero_sinistre)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                } catch (Exception $e) {
                    Log::error('Erreur lors de l\'envoi d\'email au service sinistres', [
                        'email_destination' => $this->gestionnaires,
                        'sinistre_id' => $this->sinistre->id,
                        'numero_sinistre' => $this->sinistre->numero_sinistre,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            } else {
                foreach ($this->gestionnaires as $gestionnaire) {
                    try {
                        Mail::send('emails.nouveau-sinistre', $dataEmail, function ($message) use ($gestionnaire) {
                            $message->to($gestionnaire->email, $gestionnaire->nom_complet)
                                ->subject('Nouveau sinistre déclaré - N° ' . $this->sinistre->numero_sinistre)
                                ->from(config('mail.from.address'), config('mail.from.name'));
                        });
                    } catch (Exception $e) {
                        Log::error('Erreur lors de l\'envoi d\'email à un gestionnaire', [
                            'gestionnaire_email' => $gestionnaire->email,
                            'sinistre_id' => $this->sinistre->id,
                            'numero_sinistre' => $this->sinistre->numero_sinistre,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Erreur générale lors de l\'envoi des emails aux gestionnaires', [
                'sinistre_id' => $this->sinistre->id,
                'numero_sinistre' => $this->sinistre->numero_sinistre,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * This method is triggered when the queue job fails after all retry attempts.
     * It logs an error message with details about the sinistre and the error
     * that caused the failure.
     *
     * @param Exception $exception The exception that caused the job to fail.
     */

    public function failed(Exception $exception): void
    {
        Log::error('Job d\'envoi d\'email échoué définitivement', [
            'sinistre_id' => $this->sinistre->id,
            'numero_sinistre' => $this->sinistre->numero_sinistre,
            'error' => $exception->getMessage()
        ]);
    }
}
