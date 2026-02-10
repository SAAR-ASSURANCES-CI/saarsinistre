<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expertise extends Model
{
    use HasFactory;

    protected $fillable = [
        'sinistre_id',
        'expert_id',
        'date_expertise',
        'client_nom',
        'collaborateur_nom',
        'collaborateur_telephone',
        'collaborateur_email',
        'lieu_expertise',
        'contact_client',
        'vehicule_expertise',
        'operations',
    ];

    protected $casts = [
        'date_expertise' => 'date',
        'operations' => 'array',
    ];

    /**
     * Utiliser toujours le numéro de téléphone configuré dans .env
     */
    public function getCollaborateurTelephoneAttribute($value)
    {
        return config('expertise.default_phone', $value);
    }

    /**
     * Relation avec le sinistre
     */
    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }

    /**
     * Relation avec l'expert (utilisateur)
     */
    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id');
    }
}
