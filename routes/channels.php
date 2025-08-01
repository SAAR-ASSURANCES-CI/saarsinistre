<?php

use App\Models\Sinistre;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('sinistre.{sinistreId}', function ($user, $sinistreId) {
    $sinistre = Sinistre::find($sinistreId);
    
    if (!$sinistre) {
        return false;
    }
    
    return $user->id === $sinistre->assure_id || $user->id === $sinistre->gestionnaire_id;
});