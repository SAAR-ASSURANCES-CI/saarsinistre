<?php

namespace App\Console\Commands;

use App\Models\Sinistre;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class TestSinistreNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinistre:test-notification {phone} {nom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste le système complet de notifications (email + SMS) pour un sinistre';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $nom = $this->argument('nom');
        
        $this->info("🚗 Test complet de notification de sinistre");
        $this->info("📞 Téléphone: {$phone}");
        $this->info("👤 Nom: {$nom}");
        $this->info("🌍 Environnement: " . app()->environment());
        $this->info("🔒 SSL: " . (app()->environment('production') ? 'activé' : 'désactivé'));
        
        try {
            // Créer un sinistre de test
            $sinistre = new Sinistre();
            $sinistre->id = 9999;
            $sinistre->numero_sinistre = 'TEST-NOTIF-' . now()->format('His') . '-2025';
            $sinistre->telephone_assure = $phone;
            $sinistre->nom_assure = $nom;
            $sinistre->email_assure = 'test@example.com';
            $sinistre->type_sinistre = 'collision';
            $sinistre->date_sinistre = now();
            $sinistre->lieu_sinistre = 'Test Location';
            
            $this->info("📋 Sinistre créé: {$sinistre->numero_sinistre}");
            
            // Tester le service de notification
            $notificationService = new NotificationService();
            
            $this->info("📤 Déclenchement des notifications...");
            $notificationService->triggerSinistreNotifications($sinistre);
            
            $this->line("✅ Notifications déclenchées avec succès!");
            $this->warn("📋 Vérifiez les logs pour les détails d'envoi");
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors du test de notification:");
            $this->error($e->getMessage());
            $this->error("📍 Fichier: " . $e->getFile() . ":" . $e->getLine());
            
            return 1;
        }
        
        return 0;
    }
}
