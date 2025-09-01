<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupTempUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:cleanup {--hours=2 : Supprimer les fichiers plus anciens que X heures}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les fichiers temporaires d\'upload non utilisés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoursOld = (int) $this->option('hours');
        $tempDirectory = 'temp_uploads';
        
        $this->info("Nettoyage des fichiers temporaires plus anciens que {$hoursOld} heures...");
        
        try {
            if (!Storage::disk('public')->exists($tempDirectory)) {
                $this->info('Aucun dossier temporaire trouvé.');
                return 0;
            }

            $directories = Storage::disk('public')->directories($tempDirectory);
            $deletedSessions = 0;
            $deletedFiles = 0;

            foreach ($directories as $sessionDir) {
                $lastModified = Storage::disk('public')->lastModified($sessionDir);
                $hoursAgo = now()->subHours($hoursOld)->timestamp;

                if ($lastModified < $hoursAgo) {
                    // Compter les fichiers avant suppression
                    $files = Storage::disk('public')->allFiles($sessionDir);
                    $deletedFiles += count($files);
                    
                    // Supprimer le dossier de session complet
                    Storage::disk('public')->deleteDirectory($sessionDir);
                    $deletedSessions++;
                    
                    $this->line("Supprimé: {$sessionDir} (" . count($files) . " fichiers)");
                }
            }

            if ($deletedSessions > 0) {
                $this->info("✅ Nettoyage terminé: {$deletedSessions} sessions supprimées ({$deletedFiles} fichiers au total)");
                Log::info("Nettoyage automatique temp uploads: {$deletedSessions} sessions, {$deletedFiles} fichiers");
            } else {
                $this->info("✅ Aucun fichier temporaire à nettoyer.");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Erreur lors du nettoyage: " . $e->getMessage());
            Log::error("Erreur nettoyage temp uploads: " . $e->getMessage());
            return 1;
        }
    }
}
