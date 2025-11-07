<?php

namespace App\Logging;

use Illuminate\Support\Facades\Auth;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

/**
 * CustomLogger - Logger personnalisé avec organisation hiérarchique des logs
 * 
 * Organisation des fichiers :
 * - Chemin : storage/logs/YYYY/MM/DD/immo_HH.log
 * - Exemple : storage/logs/2025/11/07/immo_18.log
 * 
 * Activation :
 * 1. Par défaut pour toute l'application :
 *    Ajoutez dans votre fichier .env : LOG_CHANNEL=custom
 * 
 * 2. Pour des logs spécifiques dans votre code :
 *    Log::channel('custom')->info('Votre message', ['contexte' => 'valeur']);
 *    Log::channel('custom')->error('Erreur détectée', ['user_id' => $userId]);
 * 
 * Fonctionnalités :
 * - Organisation automatique par date et heure
 * - Enrichissement automatique avec IP, URL, méthode HTTP
 * - Ajout automatique de l'ID utilisateur et email si connecté
 * - Informations de debug (fichier/ligne d'origine du log)
 * - Niveau de log configurable via LOG_LEVEL dans .env
 */
class CustomLogger
{
    public function __invoke(array $config): Logger
    {
        $hour = date('H');
        $logDirectory = storage_path("logs/" . date('Y/m/d'));
        
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0775, true);
        }
        
        $logPath = "$logDirectory/immo_{$hour}.log";
        
        $level = $config['level'] ?? Logger::DEBUG;
        
        $handler = new StreamHandler($logPath, $level);
        
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat, true, true);
        $handler->setFormatter($formatter);
        
        // Créer le logger
        $logger = new Logger('custom', [$handler]);
        
        $logger->pushProcessor(new WebProcessor()); 
        $logger->pushProcessor(new IntrospectionProcessor()); 
        
        $logger->pushProcessor(function ($record) {
            if (Auth::check()) {
                $record['extra']['user_id'] = Auth::id();
                $record['extra']['user_email'] = Auth::user()->email;
            }
            return $record;
        });
        
        return $logger;
    }
}
