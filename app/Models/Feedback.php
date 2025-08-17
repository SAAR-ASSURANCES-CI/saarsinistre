<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'sinistre_id',
        'assure_id',
        'note_service',
        'humeur_emoticon',
        'commentaire',
        'date_feedback',
        'envoye_automatiquement'
    ];

    protected $casts = [
        'date_feedback' => 'datetime',
        'envoye_automatiquement' => 'boolean',
    ];

    /**
     * Relation avec le sinistre
     */
    public function sinistre(): BelongsTo
    {
        return $this->belongsTo(Sinistre::class);
    }

    /**
     * Relation avec l'assuré
     */
    public function assure(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assure_id');
    }

    /**
     * Obtenir le libellé de l'humeur
     */
    public function getHumeurLibelleAttribute(): string
    {
        $humeurs = [
            '😊' => 'Très satisfait',
            '🙂' => 'Satisfait',
            '😐' => 'Neutre',
            '😕' => 'Mécontent',
            '😠' => 'Très mécontent'
        ];

        return $humeurs[$this->humeur_emoticon] ?? $this->humeur_emoticon;
    }

    /**
     * Obtenir la couleur de l'humeur
     */
    public function getHumeurCouleurAttribute(): string
    {
        $couleurs = [
            '😊' => 'green',
            '🙂' => 'blue',
            '😐' => 'yellow',
            '😕' => 'orange',
            '😠' => 'red'
        ];

        return $couleurs[$this->humeur_emoticon] ?? 'gray';
    }

    /**
     * Obtenir le libellé de la note de service
     */
    public function getNoteServiceLibelleAttribute(): string
    {
        $notes = [
            1 => 'Très mécontent',
            2 => 'Mécontent',
            3 => 'Neutre',
            4 => 'Satisfait',
            5 => 'Très satisfait'
        ];

        return $notes[$this->note_service] ?? 'Non noté';
    }
}
