<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'sinistre_id',
        'marque',
        'modele',
        'immatriculation',
        'annee',
        'couleur',
        'numero_chassis',
        'type',
    ];

    protected $casts = [
        'annee' => 'integer',
    ];

    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }
}