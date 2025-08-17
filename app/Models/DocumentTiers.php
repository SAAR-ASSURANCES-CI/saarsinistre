<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTiers extends Model
{
    protected $fillable = [
        'tiers_id',
        'type_document',
        'nom_fichier',
        'chemin_fichier',
        'taille_fichier',
        'extension',
    ];

    /**
     * Relation avec le tiers
     */
    public function tiers(): BelongsTo
    {
        return $this->belongsTo(Tiers::class);
    }

    /**
     * Accessor pour l'URL du document
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin_fichier);
    }
}