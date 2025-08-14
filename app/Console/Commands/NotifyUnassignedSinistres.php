<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sinistre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendSinistreNotificationEmail;

class NotifyUnassignedSinistres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Option --date=YYYY-MM-DD permet de forcer la date de recherche.
     */
    protected $signature = 'sinistres:notify-unassigned {--date=}';

    /**
     * The console command description.
     */
    protected $description = 'Notifier tous les gestionnaires pour les sinistres non affectés déclarés la veille.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateOption = $this->option('date');
        $targetDate = $dateOption
            ? Carbon::parse($dateOption)->startOfDay()
            : now()->subDay()->startOfDay();

        $this->info('Recherche des sinistres non affectés pour la date: ' . $targetDate->toDateString());

        $sinistres = Sinistre::query()
            ->whereNull('gestionnaire_id')
            ->whereDate('created_at', $targetDate->toDateString())
            ->whereNull('derniere_notification')
            ->get();

        if ($sinistres->isEmpty()) {
            $this->info('Aucun sinistre à notifier.');
            return self::SUCCESS;
        }

        $gestionnaires = User::query()
            ->where('role', 'gestionnaire')
            ->where('actif', true)
            ->get();

        if ($gestionnaires->isEmpty()) {
            Log::warning('Aucun gestionnaire actif trouvé pour la notification des sinistres non affectés.', [
                'date_cible' => $targetDate->toDateString(),
                'nb_sinistres' => $sinistres->count(),
            ]);
            $this->warn('Aucun gestionnaire actif.');
            return self::SUCCESS;
        }

        foreach ($sinistres as $sinistre) {
            try {
                SendSinistreNotificationEmail::dispatch($sinistre, $gestionnaires);
                $sinistre->update(['derniere_notification' => now()]);
                $this->info('Notification planifiée pour le sinistre: ' . $sinistre->numero_sinistre);
            } catch (\Throwable $e) {
                Log::error('Erreur lors de la planification de notification pour un sinistre non affecté.', [
                    'sinistre_id' => $sinistre->id,
                    'numero_sinistre' => $sinistre->numero_sinistre,
                    'error' => $e->getMessage(),
                ]);
                $this->error('Echec de notification pour: ' . $sinistre->numero_sinistre);
            }
        }

        return self::SUCCESS;
    }
}


