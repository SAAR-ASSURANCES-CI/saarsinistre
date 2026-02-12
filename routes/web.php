<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SuiviSinistreController;
use App\Jobs\SendChatMessageNotificationEmail;
use App\Models\Message;
use App\Models\Sinistre;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/suivi-sinistre', [SuiviSinistreController::class, 'rechercher'])
    ->name('suivi.rechercher');

Route::prefix('api/upload')->group(function () {
    Route::post('/chunk', [UploadController::class, 'uploadChunk'])->name('upload.chunk');
    Route::post('/finalize', [UploadController::class, 'finalizeUpload'])->name('upload.finalize');
    Route::post('/cleanup', [UploadController::class, 'cleanupSession'])->name('upload.cleanup');
});

require __DIR__.'/assures.php';
require __DIR__.'/gestionnaires.php';

