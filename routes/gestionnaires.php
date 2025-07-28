<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes accessibles uniquement aux gestionnaires et admins authentifiés
Route::middleware(['auth', 'role:admin,gestionnaire'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        // Sinistres
        Route::prefix('sinistres')->name('sinistres.')->group(function () {
            Route::get('/', [DashboardController::class, 'getSinistres'])->name('index');
            Route::get('/{sinistre}/details', [DashboardController::class, 'getDetails'])->name('details');
            Route::post('/{sinistre}/assign', [DashboardController::class, 'assignerGestionnaire'])->name('assign');
            Route::post('/{sinistre}/status', [DashboardController::class, 'changerStatut'])->name('status');
            Route::get('/en-retard', [DashboardController::class, 'getSinistresEnRetard'])->name('retard');
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
    });

    // Chat sinistre
    Route::get('/sinistres/{sinistre}/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/sinistres/{sinistre}/chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/sinistres/{sinistre}/chat', [ChatController::class, 'store'])->name('chat.store');

    // Notifications non lues
    Route::get('/notifications/unread-messages', [NotificationController::class, 'unreadMessages']);
    Route::get('/notifications/unread-messages/count', [NotificationController::class, 'unreadMessagesCount']);

    Route::get('/storage/sinistres/{sinistreId}/{filename}', function ($sinistreId, $filename) {
        $path = "sinistres/{$sinistreId}/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path("app/public/{$path}"));
    })->where('filename', '.*')->name('sinistre.document');
});
