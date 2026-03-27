<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Pour générer des tokens d'authentification



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

   
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'client' ou 'admin'
    ];

    /**
     * Les champs cachés (jamais renvoyés dans les réponses JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Transformations automatiques des types
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Le mot de passe est hashé automatiquement
    ];

    /**
     * Vérifie si l'utilisateur est administrateur
     * Utilisation : if ($user->estAdmin()) { ... }
     */
    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relation : Un utilisateur peut avoir plusieurs réservations
     * Utilisation : $user->reservations → donne toutes ses réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
