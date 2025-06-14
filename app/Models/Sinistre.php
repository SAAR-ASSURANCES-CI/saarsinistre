<?php
// app/Models/Sinistre.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sinistre extends Model
{
    protected $fillable = [
        'numero_sinistre', 'nom_assure', 'email_assure', 'telephone_assure',
        'numero_police', 'date_sinistre', 'lieu_sinistre', 'circonstances',
        'conducteur_nom', 'constat_autorite', 'officier_nom', 'commissariat',
        'dommages_releves', 'statut', 'gestionnaire_id', 'montant_estime',
        'montant_regle'
    ];

    protected $casts = [
        'date_sinistre' => 'date',
        'constat_autorite' => 'boolean',
        'date_affectation' => 'datetime',
        'date_reglement' => 'datetime',
        'derniere_notification' => 'datetime',
        'en_retard' => 'boolean',
        'montant_estime' => 'decimal:2',
        'montant_regle' => 'decimal:2',
    ];

    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestionnaire_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentSinistre::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sinistre) {
            $sinistre->numero_sinistre = 'SIN-' . date('Y') . '-' . str_pad(
                static::whereYear('created_at', date('Y'))->count() + 1,
                5, '0', STR_PAD_LEFT
            );
        });
    }

    public function scopeEnRetard($query)
    {
        return $query->where('en_retard', true);
    }

    public function scopeParGestionnaire($query, $gestionnaireId)
    {
        return $query->where('gestionnaire_id', $gestionnaireId);
    }

    public function calculerJoursEnCours()
    {
        $this->jours_en_cours = $this->created_at->diffInDays(now());
        $this->en_retard = $this->jours_en_cours > 15; // Seuil de 15 jours
        $this->save();
    }
}
