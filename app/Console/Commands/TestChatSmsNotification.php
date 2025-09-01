<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Sinistre;
use App\Models\Message;
use App\Jobs\SendChatMessageNotificationSms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestChatSmsNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:chat-sms-notification 
                           {--sinistre-id= : ID du sinistre pour le test}
                           {--assure-id= : ID de l\'assuré pour le test}
                           {--gestionnaire-id= : ID du gestionnaire pour le test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi de SMS de notification de nouveau message chat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info("📱 Test d'envoi de SMS de notification de message chat");
            $this->info("=" . str_repeat("=", 50));

            // Récupérer les paramètres
            $sinistreId = $this->option('sinistre-id');
            $assureId = $this->option('assure-id');
            $gestionnaireId = $this->option('gestionnaire-id');

            // Si pas de paramètres, prendre les premiers disponibles
            if (!$sinistreId) {
                $sinistre = Sinistre::whereNotNull('assure_id')->whereNotNull('gestionnaire_id')->first();
                if (!$sinistre) {
                    $this->error("Aucun sinistre trouvé avec assuré et gestionnaire assignés");
                    return 1;
                }
            } else {
                $sinistre = Sinistre::find($sinistreId);
                if (!$sinistre) {
                    $this->error("Sinistre avec ID {$sinistreId} non trouvé");
                    return 1;
                }
            }

            // Récupérer l'assuré
            if ($assureId) {
                $assure = User::find($assureId);
            } else {
                $assure = User::find($sinistre->assure_id);
            }

            if (!$assure) {
                $this->error("Assuré non trouvé");
                return 1;
            }

            // Récupérer le gestionnaire
            if ($gestionnaireId) {
                $gestionnaire = User::find($gestionnaireId);
            } else {
                $gestionnaire = User::find($sinistre->gestionnaire_id);
            }

            if (!$gestionnaire) {
                $this->error("Gestionnaire non trouvé");
                return 1;
            }

            $this->info("Sinistre: {$sinistre->numero_sinistre}");
            $telephone = $assure->telephone ?? $assure->numero_telephone ?? 'Pas de téléphone';
            $this->info("Assuré: {$assure->nom_complet} ({$telephone})");
            $this->info("Gestionnaire: {$gestionnaire->nom_complet}");

            // Créer un message de test
            $message = Message::create([
                'sinistre_id' => $sinistre->id,
                'sender_id' => $gestionnaire->id,
                'receiver_id' => $assure->id,
                'contenu' => 'Message de test pour notification SMS - ' . now()->format('d/m/Y H:i:s'),
                'lu' => false,
            ]);

            $this->info("✓ Message de test créé (ID: {$message->id})");

            // Déclencher l'envoi du SMS
            $this->info("📤 Dispatch du job SendChatMessageNotificationSms...");
            SendChatMessageNotificationSms::dispatch($message, $assure);

            $this->info("✅ Job dispatché avec succès!");
            $this->info("📧 Un SMS sera envoyé à l'assuré dans quelques secondes...");
            $this->info("");
            $this->info("💡 Vérifiez les logs pour confirmer l'envoi:");
            $this->info("   tail -f storage/logs/laravel.log | grep 'SMS de notification'");

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors du test:");
            $this->error($e->getMessage());
            
            Log::error('Erreur lors du test SMS chat notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }

        return 0;
    }
}
