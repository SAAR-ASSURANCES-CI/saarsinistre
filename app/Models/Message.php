<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sinistre_id',
        'sender_id',
        'receiver_id',
        'contenu',
        'lu',
    ];

    public function sinistre() {
        return $this->belongsTo(Sinistre::class);
    }
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
