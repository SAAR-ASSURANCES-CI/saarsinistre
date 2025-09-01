<?php

namespace App\Console\Commands;

use App\Models\Sinistre;
use App\Jobs\SendSinistreStatusUpdateSms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestSmsStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test-status {sinistre_id} {nouveau_statut}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi de SMS de changement de statut';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sinistreId = $this->argument('sinistre_id');
        $nouveauStatut = $this->argument('nouveau_statut');

        $statutsValides = ['en_attente', 'en_cours', 'expertise_requise', 'en_attente_documents', 'pret_reglement', 'regle', 'refuse', 'clos'];

        if (!in_array($nouveauStatut, $statutsValides)) {
            $this->error('Statut invalide. Statuts valides: ' . implode(', ', $statutsValides));
            return 1;
        }

        try {
            $sinistre = Sinistre::findOrFail($sinistreId);
            $ancienStatut = $sinistre->statut;

            if ($ancienStatut === $nouveauStatut) {
                $this->error('Le nouveau statut est identique au statut actuel.');
                return 1;
            }

            $this->info("Test de changement de statut du sinistre {$sinistre->numero_sinistre}");
            $this->info("Ancien statut: {$ancienStatut}");
            $this->info("Nouveau statut: {$nouveauStatut}");
            
            // Changer le statut
            $sinistre->update(['statut' => $nouveauStatut]);

            // Déclencher manuellement l'envoi du SMS pour le test
            SendSinistreStatusUpdateSms::dispatch($sinistre, $ancienStatut);

            $this->info("✅ Statut modifié avec succès !");
            $this->info("📧 Un SMS sera envoyé à l'assuré dans quelques secondes...");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Erreur lors du test: " . $e->getMessage());
            Log::error('Erreur lors du test SMS status update', [
                'sinistre_id' => $sinistreId,
                'nouveau_statut' => $nouveauStatut,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }
}
