<?php

namespace App\Console\Commands;

use App\Jobs\SendChatMessageNotificationEmail;
use App\Models\Message;
use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Console\Command;

class TestChatNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:chat-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the chat notification email functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début du test de notification email pour les messages de chat...');

        // Créer un gestionnaire test
        $gestionnaire = User::where('role', 'gestionnaire')->first();
        
        if (!$gestionnaire) {
            $gestionnaire = User::create([
                'nom_complet' => 'Gestionnaire Test',
                'email' => 'gestionnaire.test@saar-assurance.ci',
                'role' => 'gestionnaire',
                'actif' => true,
                'password' => bcrypt('password123'),
            ]);
            $this->info('Gestionnaire de test créé : ' . $gestionnaire->email);
        } else {
            $this->info('Utilisation du gestionnaire existant : ' . $gestionnaire->email);
        }

        // Créer un assuré test
        $assure = User::where('role', 'assure')->first();
        
        if (!$assure) {
            $assure = User::create([
                'nom_complet' => 'Assuré Test',
                'email' => 'assure.test@email.ci',
                'role' => 'assure',
                'actif' => true,
                'password' => bcrypt('password123'),
            ]);
            $this->info('Assuré de test créé : ' . $assure->email);
        } else {
            $this->info('Utilisation de l\'assuré existant : ' . $assure->email);
        }

        // Créer un sinistre test
        $sinistre = Sinistre::create([
            'numero_sinistre' => 'TEST-' . date('YmdHis'),
            'nom_assure' => $assure->nom_complet,
            'email_assure' => $assure->email,
            'telephone_assure' => '+225 07 12 34 56 78',
            'numero_police' => 'POL-2025-TEST',
            'date_sinistre' => now(),
            'heure_sinistre' => now()->format('H:i'),
            'lieu_sinistre' => 'Test Location, Abidjan',
            'circonstances' => 'Test de notification email pour nouveau message',
            'conducteur_nom' => $assure->nom_complet,
            'statut' => 'en_cours',
            'gestionnaire_id' => $gestionnaire->id,
            'assure_id' => $assure->id,
            'montant_estime' => 500000,
        ]);

        $this->info('Sinistre de test créé : ' . $sinistre->numero_sinistre);

        // Créer un message test
        $message = Message::create([
            'sinistre_id' => $sinistre->id,
            'sender_id' => $assure->id,
            'receiver_id' => $gestionnaire->id,
            'contenu' => 'Bonjour, ceci est un message de test pour vérifier les notifications email. J\'ai une question concernant mon sinistre.',
            'lu' => false,
        ]);

        $this->info('Message de test créé avec l\'ID : ' . $message->id);

        // Déclencher le job d'envoi d'email
        try {
            SendChatMessageNotificationEmail::dispatch($message, $gestionnaire);
            $this->info('✅ Job d\'envoi d\'email dispatché avec succès !');
            $this->info('📧 Un email devrait être envoyé à : ' . $gestionnaire->email);
            $this->info('📄 Vérifiez les logs d\'email ou votre boîte de réception de test.');
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du dispatch du job : ' . $e->getMessage());
            return 1;
        }

        $this->info('');
        $this->info('Test terminé avec succès !');
        $this->info('Pour nettoyer les données de test, vous pouvez supprimer :');
        $this->info('- Sinistre ID: ' . $sinistre->id);
        $this->info('- Message ID: ' . $message->id);
        
        return 0;
    }
}
