<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationController;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('declaration')->name('declaration.')->group(function () {
    Route::get('/formulaire', [DeclarationController::class, 'create'])->name('create');
    Route::post('/store', [DeclarationController::class, 'store'])->name('store');
    Route::get('/confirmation/{sinistre}', [DeclarationController::class, 'confirmation'])->name('confirmation');

    Route::get('/statut/{numeroSinistre}', [DeclarationController::class, 'statut'])->name('statut');
    Route::get('/{sinistre}/recu', [DeclarationController::class, 'downloadRecu'])->name('recu');
});


Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {

        Route::get('/sinistres', [DashboardController::class, 'getSinistres'])->name('sinistres');

        Route::get('/sinistres/{sinistre}/details', [DashboardController::class, 'getDetails'])->name('sinistres.details');

        Route::post('/sinistres/{sinistre}/assign', [DashboardController::class, 'assignerGestionnaire'])->name('sinistres.assign');

        Route::post('/sinistres/{sinistre}/status', [DashboardController::class, 'changerStatut'])->name('sinistres.status');

        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');

        Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/mark-read', [DashboardController::class, 'markNotificationsAsRead'])->name('notifications.mark-read');

        Route::get('/search', [DashboardController::class, 'searchSinistres'])->name('search');

        Route::get('/sinistres-en-retard', [DashboardController::class, 'getSinistresEnRetard'])->name('sinistres.retard');
    });
});
