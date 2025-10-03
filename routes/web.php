<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UploadController;
use App\Jobs\SendChatMessageNotificationEmail;
use App\Models\Message;
use App\Models\Sinistre;
use App\Models\User;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Routes d'upload
Route::prefix('api/upload')->group(function () {
    Route::post('/chunk', [UploadController::class, 'uploadChunk'])->name('upload.chunk');
    Route::post('/finalize', [UploadController::class, 'finalizeUpload'])->name('upload.finalize');
    Route::post('/cleanup', [UploadController::class, 'cleanupSession'])->name('upload.cleanup');
});

// Inclusion des routes spécifiques
require __DIR__.'/assures.php';
require __DIR__.'/gestionnaires.php';

