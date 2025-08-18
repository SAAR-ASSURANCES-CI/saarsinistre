<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'telephone',
        'code',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean'
    ];

    /**
     * Vérifier si le code a expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Marquer le code comme utilisé
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Scope pour récupérer les codes valides (non expirés et non utilisés)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('used', false);
    }

    /**
     * Générer un nouveau code de 6 chiffres
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Créer un nouveau code de réinitialisation
     */
    public static function createForPhone(string $telephone): self
    {
        // Invalider les anciens codes pour ce téléphone
        self::where('telephone', $telephone)->update(['used' => true]);

        return self::create([
            'telephone' => $telephone,
            'code' => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(10), 
            'used' => false
        ]);
    }
}
