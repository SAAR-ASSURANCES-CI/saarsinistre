<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Jobs\SendChatMessageNotificationEmail;
use App\Models\Message;
use App\Models\Sinistre;
use App\Models\User;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Route temporaire pour tester l'email de notification
Route::get('/test-email-notification', function () {
    
    // Trouver ou créer un gestionnaire de test
    $gestionnaire = User::where('role', 'gestionnaire')->first();
    if (!$gestionnaire) {
        $gestionnaire = User::create([
            'nom_complet' => 'Gestionnaire Test',
            'email' => 'test@example.com', // Remplacez par votre email de test
            'role' => 'gestionnaire',
            'actif' => true,
            'password' => bcrypt('password'),
        ]);
    }
    
    // Trouver ou créer un assuré de test
    $assure = User::where('role', 'assure')->first();
    if (!$assure) {
        $assure = User::create([
            'nom_complet' => 'Assuré Test',
            'email' => 'assure@example.com',
            'role' => 'assure',
            'actif' => true,
            'password' => bcrypt('password'),
        ]);
    }
    
    // Créer un sinistre de test
    $sinistre = Sinistre::create([
        'numero_sinistre' => 'TEST-' . time(),
        'nom_assure' => $assure->nom_complet,
        'email_assure' => $assure->email,
        'telephone_assure' => '+225 07 12 34 56 78',
        'numero_police' => 'POL-TEST-' . time(),
        'date_sinistre' => now(),
        'lieu_sinistre' => 'Test Location',
        'circonstances' => 'Test de notification',
        'conducteur_nom' => $assure->nom_complet,
        'statut' => 'en_cours',
        'gestionnaire_id' => $gestionnaire->id,
        'assure_id' => $assure->id,
        'montant_estime' => 100000,
    ]);
    
    // Créer un message de test
    $message = Message::create([
        'sinistre_id' => $sinistre->id,
        'sender_id' => $assure->id,
        'receiver_id' => $gestionnaire->id,
        'contenu' => 'Ceci est un message de test pour la notification email.',
        'lu' => false,
    ]);
    
    // Dispatcher le job immédiatement (sync)
    SendChatMessageNotificationEmail::dispatchSync($message, $gestionnaire);
    
    return response()->json([
        'status' => 'success',
        'message' => 'Email envoyé avec succès',
        'data' => [
            'gestionnaire_email' => $gestionnaire->email,
            'message_id' => $message->id,
            'sinistre_numero' => $sinistre->numero_sinistre
        ]
    ]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/sinistres/{sinistre}/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/sinistres/{sinistre}/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/sinistres/{sinistre}/chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');
});

// Inclusion des routes spécifiques
require __DIR__.'/assures.php';
require __DIR__.'/gestionnaires.php';

