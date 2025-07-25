<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});


// Inclusion des routes spécifiques
require __DIR__.'/assures.php';
require __DIR__.'/gestionnaires.php';

