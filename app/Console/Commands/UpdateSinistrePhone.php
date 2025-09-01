<?php

namespace App\Console\Commands;

use App\Models\Sinistre;
use Illuminate\Console\Command;

class UpdateSinistrePhone extends Command
{
    protected $signature = 'update:sinistre-phone {sinistre_id} {telephone}';
    protected $description = 'Mettre à jour le numéro de téléphone d\'un sinistre';

    public function handle()
    {
        $sinistreId = $this->argument('sinistre_id');
        $telephone = $this->argument('telephone');
        
        $sinistre = Sinistre::find($sinistreId);
        
        if (!$sinistre) {
            $this->error("Sinistre ID {$sinistreId} non trouvé");
            return 1;
        }
        
        $ancien = $sinistre->telephone_assure;
        $sinistre->telephone_assure = $telephone;
        $sinistre->save();
        
        $this->info("✅ Téléphone du sinistre {$sinistre->numero_sinistre} mis à jour:");
        $this->info("   Ancien: " . ($ancien ?: 'AUCUN'));
        $this->info("   Nouveau: {$telephone}");
        
        return 0;
    }
}
