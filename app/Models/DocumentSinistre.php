<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentSinistre extends Model
{
    protected $appends = ['url'];
    protected $fillable = [
        'sinistre_id',
        'type_document',
        'libelle_document',
        'nom_fichier',
        'nom_fichier_stocke',
        'chemin_fichier',
        'type_mime',
        'taille_fichier',
        'statut_verification',
        'commentaire_verification',
        'verifie_par',
        'verifie_le'
    ];

    protected $casts = [
        'taille_fichier' => 'integer',
        'verifie_le' => 'datetime',
    ];

    /**
     * Relation avec le sinistre
     */
    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }

    /**
     * Relation avec l'utilisateur qui a vérifié
     */
    public function verificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifie_par');
    }

    /**
     * Obtenir l'URL publique du document
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->chemin_fichier);
    }

    /**
     * Obtenir la taille formatée du fichier
     */
    public function getTailleFormateeAttribute(): string
    {
        $bytes = $this->taille_fichier;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Vérifier si le document est une image
     */
    public function getEstImageAttribute(): bool
    {
        return in_array($this->type_mime, [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ]);
    }

    /**
     * Obtenir l'icône du type de fichier
     */
    public function getIconeTypeAttribute(): string
    {
        if ($this->est_image) {
            return '🖼️';
        }

        if ($this->type_mime === 'application/pdf') {
            return '📄';
        }

        return '📎';
    }

    /**
     * Scopes
     */
    public function scopeParType($query, string $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopeVerifies($query)
    {
        return $query->where('statut_verification', 'verifie');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut_verification', 'en_attente');
    }

    /**
     * Supprimer le fichier physique lors de la suppression du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if (Storage::exists($document->chemin_fichier)) {
                Storage::delete($document->chemin_fichier);
            }
        });
    }
}
