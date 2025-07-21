<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Sinistre extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'numero_sinistre',
        'nom_assure',
        'email_assure',
        'telephone_assure',
        'numero_police',

        'assure_id',


        'date_sinistre',
        'heure_sinistre',
        'lieu_sinistre',
        'circonstances',
        'conducteur_nom',
        'constat_autorite',
        'officier_nom',
        'commissariat',
        'dommages_releves',

        'statut',
        'gestionnaire_id',
        'montant_estime',
        'montant_regle',
        'jours_en_cours',
        'en_retard',
        'date_affectation',
        'date_reglement',
        'derniere_notification'
    ];

    protected $casts = [
        'date_sinistre' => 'date',
        'heure_sinistre' => 'datetime:H:i:s',
        'date_affectation' => 'datetime',
        'date_reglement' => 'datetime',
        'derniere_notification' => 'datetime',
        'en_retard' => 'boolean',
        'montant_estime' => 'decimal:2',
        'montant_regle' => 'decimal:2',
        'jours_en_cours' => 'integer',
    ];

    /**
     * Relation avec le gestionnaire
     */
    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestionnaire_id');
    }

    /**
     *
     * @return BelongsTo<User, Sinistre>
     */
    public function assure(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assure_id');
    }

    /**
     * Relation avec les documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(DocumentSinistre::class);
    }

    /**
     * Boot method pour générer le numéro de sinistre
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sinistre) {
            $sinistre->numero_sinistre = self::genererNumeroSinistre();
        });

        static::created(function ($sinistre) {
            $sinistre->calculerJoursEnCours();
        });
    }

    /**
     * Générer un numéro de sinistre unique
     */
    private static function genererNumeroSinistre(): string
    {
        $annee = date('Y');
        $compteur = static::whereYear('created_at', $annee)->count() + 1;

        return 'APP-' . str_pad($compteur, 5, '0', STR_PAD_LEFT) . '-' . $annee;
    }

    /**
     * Scopes pour les requêtes
     */
    public function scopeEnRetard($query)
    {
        return $query->where('en_retard', true);
    }

    public function scopeParGestionnaire($query, $gestionnaireId)
    {
        return $query->where('gestionnaire_id', $gestionnaireId);
    }

    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }

    public function scopeRegles($query)
    {
        return $query->where('statut', 'regle');
    }

    /**
     * Calculer et mettre à jour les jours en cours
     */
    public function calculerJoursEnCours(): void
    {
        $createdAt = $this->getAttribute('created_at');
        $this->jours_en_cours = $createdAt ? $createdAt->diffInDays(now()) : 0;
        $this->en_retard = $this->jours_en_cours > 15; // Seuil de 15 jours
        $this->save();
    }

    /**
     * Assigner un gestionnaire au sinistre
     */
    public function assignerGestionnaire($gestionnaireId): void
    {
        $this->update([
            'gestionnaire_id' => $gestionnaireId,
            'date_affectation' => now(),
            'statut' => 'en_cours'
        ]);
    }

    /**
     * Marquer le sinistre comme réglé
     */
    public function reglerSinistre($montant): void
    {
        $this->update([
            'statut' => 'regle',
            'montant_regle' => $montant,
            'date_reglement' => now()
        ]);
    }

    /**
     * Obtenir le libellé du statut en français
     */
    public function getStatutLibelleAttribute(): string
    {
        $statuts = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours de traitement',
            'expertise_requise' => 'Expertise requise',
            'en_attente_documents' => 'En attente de documents',
            'pret_reglement' => 'Prêt pour règlement',
            'regle' => 'Réglé',
            'refuse' => 'Refusé',
            'clos' => 'Clos'
        ];

        $statut = $this->getAttribute('statut');
        return $statuts[$statut] ?? $statut;
    }

    /**
     * Obtenir la couleur du badge pour le statut
     */
    public function getStatutCouleurAttribute(): string
    {
        $couleurs = [
            'en_attente' => 'yellow',
            'en_cours' => 'blue',
            'expertise_requise' => 'purple',
            'en_attente_documents' => 'orange',
            'pret_reglement' => 'indigo',
            'regle' => 'green',
            'refuse' => 'red',
            'clos' => 'gray'
        ];

        $statut = $this->getAttribute('statut');
        return $couleurs[$statut] ?? 'gray';
    }

    /**
     * Vérifier si le sinistre peut être modifié
     */
    public function peutEtreModifie(): bool
    {
        return !in_array($this->getAttribute('statut'), ['regle', 'refuse', 'clos']);
    }

    /**
     * Obtenir le pourcentage de documents vérifiés
     */
    public function getPourcentageDocumentsVerifiesAttribute(): float
    {
        $totalDocuments = $this->documents->count();

        if ($totalDocuments === 0) {
            return 0;
        }

        $documentsVerifies = $this->documents->where('statut_verification', 'verifie')->count();

        return round(($documentsVerifies / $totalDocuments) * 100, 1);
    }

    /**
     * Vérifier si tous les documents sont vérifiés
     */
    public function getTousDocumentsVerifiesAttribute(): bool
    {
        return $this->documents->isNotEmpty() &&
            $this->documents->every(fn($doc) => $doc->statut_verification === 'verifie');
    }

    /**
     * Obtenir la date limite recommandée
     */
    public function getDateLimiteAttribute(): Carbon
    {
        return $this->getAttribute('created_at')->addDays(15);
    }

    /**
     * Vérifier si le sinistre est urgent
     */
    public function getEstUrgentAttribute(): bool
    {
        return $this->jours_en_cours > 10 && !$this->en_retard;
    }
}
