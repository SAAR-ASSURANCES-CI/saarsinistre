<?php

namespace App\Jobs;

use Exception;
use App\Mail\ChatMessageNotificationMail;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendChatMessageNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $gestionnaire;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message, User $gestionnaire)
    {
        $this->message = $message;
        $this->gestionnaire = $gestionnaire;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->gestionnaire->email, $this->gestionnaire->nom_complet)
                ->send(new ChatMessageNotificationMail($this->message, $this->gestionnaire));

            Log::info('Email de notification de nouveau message envoyé avec succès', [
                'gestionnaire_email' => $this->gestionnaire->email,
                'message_id' => $this->message->id,
                'sinistre_id' => $this->message->sinistre_id,
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de notification de nouveau message', [
                'gestionnaire_email' => $this->gestionnaire->email,
                'message_id' => $this->message->id,
                'sinistre_id' => $this->message->sinistre_id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Job d\'envoi d\'email de notification de nouveau message échoué', [
            'gestionnaire_email' => $this->gestionnaire->email,
            'message_id' => $this->message->id,
            'sinistre_id' => $this->message->sinistre_id,
            'error' => $exception->getMessage()
        ]);
    }
}
