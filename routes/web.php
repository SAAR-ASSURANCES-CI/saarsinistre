<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MediaController;

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
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/login/assure', [AuthController::class, 'showLoginAssureForm'])->name('login.assure');
});


Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {

        //sinistre
        Route::prefix('sinistres')->name('sinistres.')->group(function () {
            Route::get('/', [DashboardController::class, 'getSinistres'])->name('index');
            Route::get('/{sinistre}/details', [DashboardController::class, 'getDetails'])->name('details');
            Route::post('/{sinistre}/assign', [DashboardController::class, 'assignerGestionnaire'])->name('assign');
            Route::post('/{sinistre}/status', [DashboardController::class, 'changerStatut'])->name('status');
            Route::get('/en-retard', [DashboardController::class, 'getSinistresEnRetard'])->name('retard');
        });

        Route::get('/stats', [DashboardController::class, 'getStats'])->name('stats');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [DashboardController::class, 'getNotifications'])->name('index');
            Route::post('/mark-read', [DashboardController::class, 'markNotificationsAsRead'])->name('mark-read');
        });

        // Recherche
        Route::get('/search', [DashboardController::class, 'searchSinistres'])->name('search');

        // Gestion des utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        //media
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::post('/upload', [MediaController::class, 'store'])->name('store');
            Route::delete('/{document}', [MediaController::class, 'destroy'])->name('destroy');
        });
    });
});

