<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrangeService;
use App\Models\User;
use App\Models\Sinistre;
use App\Jobs\SendAccountCreationSms;
use App\Jobs\SendSinistreConfirmationSms;

class TestSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone} {message?} {--declaration : Test avec format déclaration de sinistre} {--job : Test via job comme depuis le formulaire}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Teste l\'envoi de SMS via l\'API Orange';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message');
        $isDeclaration = $this->option('declaration');
        $useJob = $this->option('job');
        
        $this->info("📱 Test d'envoi de SMS");
        $this->info("📞 Destinataire: {$phone}");
        $this->info("🌍 Environnement: " . app()->environment());
        $this->info("🔒 SSL: " . (app()->environment('production') ? 'activé' : 'désactivé'));
        $this->info("⚙️ Mode: " . ($useJob ? 'via job (comme formulaire)' : 'direct'));
        
        try {
            $orangeService = app(OrangeService::class);
            
            if (!$useJob) {
                $this->info("🔑 Récupération du token...");
                $token = $orangeService->getToken();
                $this->line("✅ Token récupéré avec succès");
            }
            
            if ($isDeclaration) {
                $nomAssure = 'KOUAME Jean';
                $numeroSinistre = 'APP-' . str_pad(rand(1, 999), 5, '0', STR_PAD_LEFT) . '-2025';
                
                $this->info("🚗 Test déclaration de sinistre");
                $this->info("👤 Assuré: {$nomAssure}");
                $this->info("📋 Numéro sinistre: {$numeroSinistre}");
                
                if ($useJob) {
                    // Trouver un sinistre existant ou créer un temporaire
                    $sinistre = Sinistre::first();
                    if (!$sinistre) {
                        $this->error("❌ Aucun sinistre trouvé en base pour le test. Créez d'abord un sinistre ou utilisez le mode direct.");
                        return 1;
                    }
                    
                    // Modifier temporairement les données pour le test
                    $originalPhone = $sinistre->telephone_assure;
                    $originalNom = $sinistre->nom_assure;
                    $originalNumero = $sinistre->numero_sinistre;
                    
                    $sinistre->telephone_assure = $phone;
                    $sinistre->nom_assure = $nomAssure;
                    $sinistre->numero_sinistre = $numeroSinistre;
                    
                    $this->info("📤 Dispatch du job SendSinistreConfirmationSms...");
                    SendSinistreConfirmationSms::dispatch($sinistre);
                    
                    // Restaurer les données originales
                    $sinistre->telephone_assure = $originalPhone;
                    $sinistre->nom_assure = $originalNom;
                    $sinistre->numero_sinistre = $originalNumero;
                    
                    $this->line("✅ Job dispatché avec succès! Vérifiez les logs pour le résultat.");
                } else {
                    $this->info("📤 Envoi du SMS de confirmation...");
                    $result = $orangeService->sendSmsConfirmationSinistre($phone, $nomAssure, $numeroSinistre);
                    $this->line("✅ SMS de confirmation de sinistre envoyé avec succès!");
                    
                    $this->info("📋 Réponse de l'API:");
                    $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            } else {
                // Test SMS simple
                $message = $message ?? 'Test SMS depuis SAAR Assurances - ' . now()->format('d/m/Y H:i:s');
                $this->info("📄 Message: {$message}");
                
                if ($useJob) {
                    // Trouver un utilisateur existant ou créer un temporaire
                    $user = User::where('role', 'assure')->first();
                    if (!$user) {
                        $this->error("❌ Aucun utilisateur trouvé en base pour le test. Créez d'abord un utilisateur ou utilisez le mode direct.");
                        return 1;
                    }
                    
                    $this->info("📤 Dispatch du job SendAccountCreationSms...");
                    SendAccountCreationSms::dispatch($user, $phone);
                    $this->line("✅ Job dispatché avec succès! Vérifiez les logs pour le résultat.");
                } else {
                    $this->info("📤 Envoi du SMS...");
                    $result = $orangeService->sendSMS($phone, $message, 'SAAR');
                    $this->line("✅ SMS envoyé avec succès!");
                    
                    $this->info("📋 Réponse de l'API:");
                    $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'envoi du SMS:");
            $this->error($e->getMessage());
            
            $this->warn("\n🔧 Suggestions de résolution:");
            $this->line("1. Vérifiez les variables d'environnement Orange (ORANGE_CLIENT_ID, ORANGE_CLIENT_SECRET, etc.)");
            $this->line("2. Vérifiez la connectivité internet");
            $this->line("3. En production, vérifiez les certificats SSL");
            $this->line("4. Vérifiez le format du numéro de téléphone (ex: +2250712345678)");
            
            return 1;
        }
        
        return 0;
    }
}
