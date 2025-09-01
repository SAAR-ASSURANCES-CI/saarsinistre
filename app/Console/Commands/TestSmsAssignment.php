<?php

namespace App\Console\Commands;

use App\Models\Sinistre;
use App\Models\User;
use App\Jobs\SendGestionnaireAssignmentSms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSmsAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test-assignment {sinistre_id} {gestionnaire_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi de SMS d\'affectation de gestionnaire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sinistreId = $this->argument('sinistre_id');
        $gestionnaireId = $this->argument('gestionnaire_id');

        try {
            $sinistre = Sinistre::findOrFail($sinistreId);
            $gestionnaire = User::findOrFail($gestionnaireId);

            if (!$gestionnaire->estGestionnaire()) {
                $this->error('L\'utilisateur spécifié n\'est pas un gestionnaire.');
                return 1;
            }

            $this->info("Test d'affectation du sinistre {$sinistre->numero_sinistre} au gestionnaire {$gestionnaire->nom_complet}");
            
            // Simuler l'affectation
            $ancienGestionnaire = $sinistre->gestionnaire_id;
            $sinistre->assignerGestionnaire($gestionnaireId);

            $this->info("✅ Sinistre affecté avec succès !");
            $this->info("📧 Un SMS sera envoyé à l'assuré dans quelques secondes...");
            
            if ($ancienGestionnaire) {
                $this->info("ℹ️  Ancien gestionnaire: {$ancienGestionnaire}");
            }
            
            $this->info("ℹ️  Nouveau gestionnaire: {$gestionnaireId}");
            $this->info("ℹ️  Statut du sinistre: {$sinistre->fresh()->statut}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Erreur lors du test: " . $e->getMessage());
            Log::error('Erreur lors du test SMS assignment', [
                'sinistre_id' => $sinistreId,
                'gestionnaire_id' => $gestionnaireId,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }
}
