<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\DasboardAssureController;
use App\Http\Controllers\FeedbackController;

//Authentification
Route::middleware(['guest'])->group(function () {
    Route::get('/login/assure', [AuthController::class, 'showLoginAssureForm'])->name('login.assure');
    Route::post('/login/assure', [AuthController::class, 'loginAssure'])->name('login.assure.post');
    
    // Routes de réinitialisation de mot de passe
    Route::get('/password/reset', [App\Http\Controllers\PasswordResetController::class, 'showRequestForm'])->name('password.reset.request');
    Route::post('/password/reset', [App\Http\Controllers\PasswordResetController::class, 'sendResetCode'])->name('password.reset.send');
    Route::get('/password/reset/verify', [App\Http\Controllers\PasswordResetController::class, 'showVerifyForm'])->name('password.reset.verify');
    Route::post('/password/reset/verify', [App\Http\Controllers\PasswordResetController::class, 'verifyCode'])->name('password.reset.verify.post');
    Route::get('/password/reset/new', [App\Http\Controllers\PasswordResetController::class, 'showNewPasswordForm'])->name('password.reset.new');
    Route::post('/password/reset/new', [App\Http\Controllers\PasswordResetController::class, 'updatePassword'])->name('password.reset.update');
});

Route::post('/assure/logout', [AuthController::class, 'logoutAssure'])->name('logout.assure');

// Routes accessibles uniquement aux assurés authentifiés
Route::middleware(['auth'])->prefix('assures')->name('assures.')->group(function () {
    // Dashboard assuré
    Route::get('/dashboard', [DasboardAssureController::class, 'index'])->name('dashboard');

    // Changement de mot de passe pour assuré
    Route::get('/password/change', [AuthController::class, 'showChangePasswordFormAssure'])->name('password.change');
    Route::post('/password/change', [AuthController::class, 'changePasswordAssure'])->name('password.change.post');

    // Chat/Discussion de sinistre
    Route::get('/sinistres/{sinistre}/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/sinistres/{sinistre}/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/sinistres/{sinistre}/chat/fetch', [ChatController::class, 'fetch'])->name('chat.fetch');
    
    // Feedback
    Route::get('/sinistres/{sinistre}/feedback', [FeedbackController::class, 'showForm'])->name('feedback.form');
    Route::post('/sinistres/{sinistre}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    
});

// Déclaration de sinistre (accessible à tous ou à restreindre selon besoin)
Route::prefix('declaration')->name('declaration.')->group(function () {
    Route::get('/formulaire', [DeclarationController::class, 'create'])->name('create');
    Route::post('/store', [DeclarationController::class, 'store'])->name('store');
    Route::get('/confirmation/{sinistre}', [DeclarationController::class, 'confirmation'])->name('confirmation');
    Route::get('/statut/{numeroSinistre}', [DeclarationController::class, 'statut'])->name('statut');
    Route::get('/{sinistre}/recu', [DeclarationController::class, 'downloadRecu'])->name('recu');
}); 
