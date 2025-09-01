<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Sinistre;
use App\Services\AssureAccountService;
use App\Services\OrangeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestAccountCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:account-creation {nom_assure} {email_assure?} {telephone_assure?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester la logique de création de compte assuré';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nomAssure = $this->argument('nom_assure');
        $emailAssure = $this->argument('email_assure');
        $telephoneAssure = $this->argument('telephone_assure');

        $this->info("=== Test de la logique de création de compte ===");
        $this->info("Nom: {$nomAssure}");
        $this->info("Email: " . ($emailAssure ?: 'non fourni'));
        $this->info("Téléphone: " . ($telephoneAssure ?: 'non fourni'));
        $this->line("");

        $data = [
            'nom_assure' => $nomAssure,
            'email_assure' => $emailAssure,
            'telephone_assure' => $telephoneAssure,
        ];

        try {
            // Simuler la logique du contrôleur
            $user = null;
            
            // 1. Chercher par email exact
            if (!empty($data['email_assure'])) {
                $user = User::where('email', $data['email_assure'])
                           ->where('role', 'assure')
                           ->first();
                           
                if ($user) {
                    $this->info("✅ Utilisateur trouvé par email: {$user->nom_complet} (ID: {$user->id})");
                } else {
                    $this->info("❌ Aucun utilisateur trouvé avec l'email: {$data['email_assure']}");
                }
            }
            
            // 2. Chercher par téléphone et nom
            if (!$user && !empty($data['telephone_assure'])) {
                $sinistreExistant = Sinistre::where('telephone_assure', $data['telephone_assure'])
                                          ->where('nom_assure', $data['nom_assure'])
                                          ->whereNotNull('assure_id')
                                          ->first();
                
                if ($sinistreExistant && $sinistreExistant->assure_id) {
                    $user = User::find($sinistreExistant->assure_id);
                    $this->info("✅ Utilisateur trouvé via sinistre existant: {$user->nom_complet} (ID: {$user->id})");
                    $this->info("   Sinistre: {$sinistreExistant->numero_sinistre}");
                } else {
                    $this->info("❌ Aucun sinistre trouvé avec téléphone: {$data['telephone_assure']} et nom: {$nomAssure}");
                }
            }

            // 3. Créer un nouveau compte si nécessaire
            if (!$user) {
                $this->info("🔄 Création d'un nouveau compte...");
                
                // Simuler la création (sans vraiment créer pour le test)
                $this->info("✅ Un nouveau compte sera créé");
                $this->info("📧 Un SMS sera envoyé avec les identifiants");
                
                // Pour un vrai test, décommentez cette ligne :
                // $user = (new AssureAccountService)->createAssureAccount($data, app(OrangeService::class));
            } else {
                $this->info("ℹ️  Utilisation du compte existant - AUCUN SMS ne sera envoyé");
            }

            $this->line("");
            $this->info("=== Résumé ===");
            if ($user) {
                $this->info("Compte utilisé: {$user->nom_complet}");
                $this->info("Email: " . ($user->email ?: 'non défini'));
                $this->info("Username: {$user->username}");
            } else {
                $this->info("Un nouveau compte serait créé");
            }

            return 0;
            
        } catch (\Exception $e) {
            $this->error("Erreur lors du test: " . $e->getMessage());
            Log::error('Erreur test account creation', [
                'nom_assure' => $nomAssure,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }
}
