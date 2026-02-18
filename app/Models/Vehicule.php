<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicule extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
        'sinistre_id',
        'marque',
        'modele',
        'immatriculation',
    ];

    protected $casts = [];

    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }
}
