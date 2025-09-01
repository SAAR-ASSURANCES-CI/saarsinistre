<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\UserCredentialsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SendUserCredentialsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $motDePasseTemporaire;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $motDePasseTemporaire)
    {
        $this->user = $user;
        $this->motDePasseTemporaire = $motDePasseTemporaire;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!$this->user->email) {
                Log::warning('Tentative d\'envoi d\'email à un utilisateur sans adresse email', [
                    'user_id' => $this->user->id,
                    'nom_complet' => $this->user->nom_complet
                ]);
                return;
            }

            Mail::to($this->user->email)->send(new UserCredentialsMail($this->user, $this->motDePasseTemporaire));

            Log::info('Email de credentials envoyé avec succès', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'nom_complet' => $this->user->nom_complet,
                'role' => $this->user->role
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de credentials', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'nom_complet' => $this->user->nom_complet,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * The number of times the job may be attempted.
     */
    public function tries(): int
    {
        return 3;
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }
}
