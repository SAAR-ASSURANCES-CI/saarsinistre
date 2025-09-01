<?php

namespace App\Console\Commands;

use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Console\Command;

class CheckSinistrePhone extends Command
{
    protected $signature = 'check:sinistre-phone {sinistre_id}';
    protected $description = 'Vérifier le numéro de téléphone d\'un sinistre';

    public function handle()
    {
        $sinistreId = $this->argument('sinistre_id');
        $sinistre = Sinistre::find($sinistreId);
        
        if (!$sinistre) {
            $this->error("Sinistre ID {$sinistreId} non trouvé");
            return 1;
        }
        
        $this->info("=== INFORMATIONS SINISTRE ===");
        $this->info("ID: {$sinistre->id}");
        $this->info("Numéro: {$sinistre->numero_sinistre}");
        $this->info("Nom assuré: {$sinistre->nom_assure}");
        $this->info("Email assuré: {$sinistre->email_assure}");
        $this->info("Téléphone assuré: " . ($sinistre->telephone_assure ?: 'AUCUN'));
        
        if ($sinistre->assure_id) {
            $assure = User::find($sinistre->assure_id);
            $this->info("=== UTILISATEUR ASSURÉ ===");
            $this->info("ID utilisateur: {$assure->id}");
            $this->info("Nom: {$assure->nom_complet}");
            $this->info("Email: {$assure->email}");
        }
        
        return 0;
    }
}
