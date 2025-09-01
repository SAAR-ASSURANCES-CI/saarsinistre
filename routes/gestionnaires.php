<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Services\PdfGenerationService;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Réinitialisation de mot de passe
Route::get('/password/forgot', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/password/reset', [AuthController::class, 'sendResetLink'])->name('password.reset.send');

// Changement de mot de passe (pour utilisateurs connectés)
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [AuthController::class, 'showChangePasswordForm'])->name('gestionnaire.password.change');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('gestionnaire.password.change.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes accessibles uniquement aux gestionnaires et admins authentifiés
Route::middleware(['auth', 'role:admin,gestionnaire', \App\Http\Middleware\CheckPasswordExpiry::class])->prefix('gestionnaires')->name('gestionnaires.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Chat/Discussion de sinistre
    Route::get('/sinistres/{sinistre}/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/sinistres/{sinistre}/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/sinistres/{sinistre}/chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        // Sinistres
        Route::prefix('sinistres')->name('sinistres.')->group(function () {
            Route::get('/', [DashboardController::class, 'getSinistres'])->name('index');
            Route::get('/{sinistre}/details', [DashboardController::class, 'getDetails'])->name('details');
            Route::post('/{sinistre}/assign', [DashboardController::class, 'assignerGestionnaire'])->name('assign');
            Route::post('/{sinistre}/status', [DashboardController::class, 'changerStatut'])->name('status');
            Route::get('/en-retard', [DashboardController::class, 'getSinistresEnRetard'])->name('retard');
            Route::get('/{sinistre}/fiche', function (PdfGenerationService $pdfService, $sinistre) {
                return $pdfService->generateSinistreFiche((int)$sinistre);
            })->name('fiche');
        });

        // Statistiques
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [DashboardController::class, 'getNotifications'])->name('index');
            Route::post('/mark-read', [DashboardController::class, 'markNotificationsAsRead'])->name('mark-read');
        });

        // Recherche
        Route::get('/search', [DashboardController::class, 'searchSinistres'])->name('search');

        // Gestion des utilisateurs (réservé aux admins)
        Route::prefix('users')->name('users.')->middleware('role:admin')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Médias
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::post('/upload', [MediaController::class, 'store'])->name('store');
            Route::delete('/{document}', [MediaController::class, 'destroy'])->name('destroy');
        });

        // Feedback
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::get('/', [FeedbackController::class, 'index'])->name('index');
            Route::get('/{feedback}', [FeedbackController::class, 'show'])->name('show');
            Route::get('/export/csv', [FeedbackController::class, 'export'])->name('export');
        });
    });

});

// Route d'accès aux fichiers de storage (en dehors du groupe gestionnaires)
Route::get('/storage/sinistres/{sinistreId}/{filename}', function ($sinistreId, $filename) {
    $path = "sinistres/{$sinistreId}/{$filename}";

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(storage_path("app/public/{$path}"));
})->where('filename', '.*')->name('sinistre.document');

Route::middleware(['auth', 'role:admin,gestionnaire'])->group(function () {
    // Notifications non lues
    Route::get('/notifications/unread-messages', [NotificationController::class, 'unreadMessages']);
    Route::get('/notifications/unread-messages/count', [NotificationController::class, 'unreadMessagesCount']);
});

// Route de debug pour vérifier les données de sinistre
Route::get('/debug/sinistre/{id}', function ($id) {
    $sinistre = \App\Models\Sinistre::find($id);
    if (!$sinistre) {
        return response()->json(['error' => 'Sinistre non trouvé'], 404);
    }
    
    return response()->json([
        'sinistre_id' => $sinistre->id,
        'numero_sinistre' => $sinistre->numero_sinistre,
        'telephone_assure' => $sinistre->telephone_assure,
        'telephone_assure_length' => strlen($sinistre->telephone_assure ?? ''),
        'telephone_assure_is_empty' => empty($sinistre->telephone_assure),
        'telephone_assure_is_null' => is_null($sinistre->telephone_assure),
        'telephone_assure_trimmed' => trim($sinistre->telephone_assure ?? ''),
        'all_attributes' => $sinistre->toArray()
    ]);
})->name('debug.sinistre');
