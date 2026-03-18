<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// 1. Importation de l'interface JWT
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

// 2. Ajout de "implements JWTSubject"
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- MÉTHODES OBLIGATOIRES POUR JWT ---

    /**
     * Récupère l'identifiant qui sera stocké dans le jeton (le ID de l'utilisateur).
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Permet d'ajouter des informations personnalisées dans le jeton.
     * Ici, on ajoute le rôle pour faciliter les vérifications côté Frontend.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }

    // --- RELATIONS ---

    public function orders() {
        return $this->hasMany(Order::class);
    }
}