<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Sinistre;
use App\Models\Message;
use App\Jobs\SendChatMessageNotificationSms;
use Illuminate\Console\Command;

class TestChatSmsFixed extends Command
{
    protected $signature = 'test:chat-sms-fixed {sinistre_id}';
    protected $description = 'Tester l\'envoi de SMS avec la logique corrigée';

    public function handle()
    {
        try {
            $sinistreId = $this->argument('sinistre_id');
            $sinistre = Sinistre::with(['assure', 'gestionnaire'])->find($sinistreId);
            
            if (!$sinistre) {
                $this->error("Sinistre ID {$sinistreId} non trouvé");
                return 1;
            }
            
            if (!$sinistre->assure_id || !$sinistre->gestionnaire_id) {
                $this->error("Le sinistre doit avoir un assuré ET un gestionnaire assignés");
                return 1;
            }
            
            $this->info("=== TEST SMS NOTIFICATION ===");
            $this->info("Sinistre: {$sinistre->numero_sinistre}");
            $this->info("Assuré: {$sinistre->nom_assure}");
            $this->info("Téléphone: " . ($sinistre->telephone_assure ?: 'AUCUN'));
            $this->info("Gestionnaire: {$sinistre->gestionnaire->nom_complet}");
            
            if (empty($sinistre->telephone_assure)) {
                $this->error("❌ ÉCHEC: Aucun numéro de téléphone renseigné pour ce sinistre");
                $this->info("💡 Pour corriger: Mettre à jour le champ 'telephone_assure' du sinistre");
                return 1;
            }
            
            // Créer un message de test
            $message = Message::create([
                'sinistre_id' => $sinistre->id,
                'sender_id' => $sinistre->gestionnaire_id,
                'receiver_id' => $sinistre->assure_id,
                'contenu' => 'Test SMS corrigé - ' . now()->format('d/m/Y H:i:s'),
                'lu' => false,
            ]);
            
            $this->info("✓ Message de test créé (ID: {$message->id})");
            
            // Déclencher le Job
            SendChatMessageNotificationSms::dispatch($message, $sinistre->assure);
            
            $this->info("✅ Job dispatché! SMS sera envoyé au {$sinistre->telephone_assure}");
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
