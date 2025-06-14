<?php

use App\Http\Controllers\DeclarationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('declaration')->name('declaration.')->group(function () {
    Route::get('/formulaire', [DeclarationController::class, 'create'])->name('create');

    Route::post('/store', [DeclarationController::class, 'store'])->name('store');

    Route::get('/confirmation/{sinistre}', [DeclarationController::class, 'confirmation'])->name('confirmation');

    // API pour vérifier le statut d'un sinistre
    Route::get('/statut/{numeroSinistre}', [DeclarationController::class, 'statut'])->name('statut');
});
