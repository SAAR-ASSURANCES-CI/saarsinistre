<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Console\Command;

class CleanTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:clean-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean test data created for chat notification testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Nettoyage des données de test...');

        // Supprimer les sinistres de test
        $testSinistres = Sinistre::where('numero_sinistre', 'LIKE', 'TEST-%')->get();
        foreach ($testSinistres as $sinistre) {
            // Supprimer les messages associés
            Message::where('sinistre_id', $sinistre->id)->delete();
            // Supprimer le sinistre
            $sinistre->delete();
            $this->info('Sinistre de test supprimé : ' . $sinistre->numero_sinistre);
        }

        // Supprimer les utilisateurs de test
        $testUsers = User::whereIn('email', [
            'gestionnaire.test@saar-assurance.ci',
            'assure.test@email.ci'
        ])->get();

        foreach ($testUsers as $user) {
            $user->delete();
            $this->info('Utilisateur de test supprimé : ' . $user->email);
        }

        $this->info('✅ Nettoyage terminé !');
        
        return 0;
    }
}
