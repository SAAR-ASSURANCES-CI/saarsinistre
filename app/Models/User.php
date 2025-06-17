<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom_complet',
        'email',
        'role',
        'actif',
        'sinistres_en_cours',
        'limite_sinistres',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function sinistres(): HasMany
    {
        return $this->hasMany(Sinistre::class, 'gestionnaire_id');
    }

    /**
     * Obtenir les sinistres en cours pour ce gestionnaire
     */
    public function sinistresEnCours(): HasMany
    {
        return $this->sinistres()->where('statut', 'en_cours');
    }

    /**
     * Obtenir les sinistres en retard pour ce gestionnaire
     */
    public function sinistresEnRetard(): HasMany
    {
        return $this->sinistres()->where('en_retard', true);
    }

    /**
     * Compter le nombre de sinistres assignés
     */
    public function getNombreSinistresAttribute(): int
    {
        return $this->sinistres()->count();
    }

    /**
     * Compter le nombre de sinistres en retard
     */
    public function getNombreSinistresEnRetardAttribute(): int
    {
        return $this->sinistresEnRetard()->count();
    }

    /**
     * Vérifier si l'utilisateur est gestionnaire
     */
    public function estGestionnaire(): bool
    {
        return $this->role === 'gestionnaire' || $this->role === 'admin';
    }

    /**
     * Scope pour récupérer seulement les gestionnaires
     */
    public function scopeGestionnaires($query)
    {
        return $query->whereIn('role', ['gestionnaire', 'admin']);
    }
}
