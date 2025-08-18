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
            
            Log::info('Compte assuré créé avec succès', [
                'user_id' => $this->user->id,
                'username' => $this->user->username,
                'nom_complet' => $this->user->nom_complet,
                'telephone' => $this->telephone,
                'numero_assure' => $this->user->numero_assure
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du compte assuré: ' . $e->getMessage());
        }
    }
}
