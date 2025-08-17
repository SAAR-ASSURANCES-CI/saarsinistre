<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tiers extends Model
{
    protected $fillable = [
        'sinistre_id',
        'numero_tiers',
        'marque_vehicule',
        'modele_vehicule',
        'immatriculation',
        'nom_conducteur',
        'prenom_conducteur',
        'telephone',
        'email',
        'adresse',
        'compagnie_assurance',
        'numero_police_assurance',
        'details_assurance',
        'details_supplementaires',
    ];

    /**
     * Relation avec le sinistre
     */
    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }

    /**
     * Relation avec les documents du tiers
     */
    public function documents(): HasMany
    {
        return $this->hasMany(DocumentTiers::class);
    }

    /**
     * Accessor pour le nom complet du conducteur
     */
    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom_conducteur . ' ' . $this->nom_conducteur);
    }

    /**
     * Accessor pour la désignation du véhicule
     */
    public function getDesignationVehiculeAttribute(): string
    {
        return trim($this->marque_vehicule . ' ' . $this->modele_vehicule);
    }
}
