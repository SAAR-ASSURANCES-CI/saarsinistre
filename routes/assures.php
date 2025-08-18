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
Route::middleware(['auth'])->group(function () {
    // Dashboard assuré
    Route::get('/assures/dashboard', [DasboardAssureController::class, 'index'])->name('assures.dashboard');

    // Changement de mot de passe pour assuré
    Route::get('/assure/password/change', [AuthController::class, 'showChangePasswordFormAssure'])->name('assure.password.change');
    Route::post('/assure/password/change', [AuthController::class, 'changePasswordAssure'])->name('assure.password.change.post');

    Route::get('/sinistres/{sinistre}/chat', [ChatController::class, 'index'])->name('assures.chat.index');
    Route::post('/sinistres/{sinistre}/chat', [ChatController::class, 'store'])->name('assures.chat.store');
    Route::get('/sinistres/{sinistre}/chat/fetch', [ChatController::class, 'fetch'])->name('assures.chat.fetch');
    
    // Feedback
    Route::get('/sinistres/{sinistre}/feedback', [FeedbackController::class, 'showForm'])->name('assures.feedback.form');
    Route::post('/sinistres/{sinistre}/feedback', [FeedbackController::class, 'store'])->name('assures.feedback.store');
    
});

// Déclaration de sinistre (accessible à tous ou à restreindre selon besoin)
Route::prefix('declaration')->name('declaration.')->group(function () {
    Route::get('/formulaire', [DeclarationController::class, 'create'])->name('create');
    Route::post('/store', [DeclarationController::class, 'store'])->name('store');
    Route::get('/confirmation/{sinistre}', [DeclarationController::class, 'confirmation'])->name('confirmation');
    Route::get('/statut/{numeroSinistre}', [DeclarationController::class, 'statut'])->name('statut');
    Route::get('/{sinistre}/recu', [DeclarationController::class, 'downloadRecu'])->name('recu');
}); 
