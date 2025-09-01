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

// Inclusion des routes spécifiques
require __DIR__.'/assures.php';
require __DIR__.'/gestionnaires.php';

