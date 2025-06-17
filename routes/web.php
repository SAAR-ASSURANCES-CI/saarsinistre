<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationController;

Route::get('/', function () {
    return view('welcome');
});

// Routes publiques pour la déclaration de sinistres
Route::prefix('declaration')->name('declaration.')->group(function () {
    Route::get('/formulaire', [DeclarationController::class, 'create'])->name('create');
    Route::post('/store', [DeclarationController::class, 'store'])->name('store');
    Route::get('/confirmation/{sinistre}', [DeclarationController::class, 'confirmation'])->name('confirmation');

    // API pour vérifier le statut d'un sinistre
    Route::get('/statut/{numeroSinistre}', [DeclarationController::class, 'statut'])->name('statut');
    Route::get('/{sinistre}/recu', [DeclarationController::class, 'downloadRecu'])->name('recu');
});

// Routes d'authentification (accessible uniquement si non connecté)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Routes d'inscription (optionnelles)
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Route de déconnexion (accessible uniquement si connecté)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Route principale du dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes API pour les données dynamiques
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        // Récupérer les sinistres avec filtres et pagination
        Route::get('/sinistres', [DashboardController::class, 'getSinistres'])->name('sinistres');

        // Détails d'un sinistre spécifique
        Route::get('/sinistres/{sinistre}/details', [DashboardController::class, 'getDetails'])->name('sinistres.details');

        // Affecter un gestionnaire
        Route::post('/sinistres/{sinistre}/assign', [DashboardController::class, 'assignerGestionnaire'])->name('sinistres.assign');

        // Changer le statut
        Route::post('/sinistres/{sinistre}/status', [DashboardController::class, 'changerStatut'])->name('sinistres.status');

        // Statistiques
        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');

        // Notifications
        Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/mark-read', [DashboardController::class, 'markNotificationsAsRead'])->name('notifications.mark-read');

        // Recherche rapide
        Route::get('/search', [DashboardController::class, 'searchSinistres'])->name('search');

        // Sinistres en retard
        Route::get('/sinistres-en-retard', [DashboardController::class, 'getSinistresEnRetard'])->name('sinistres.retard');
    });
});
