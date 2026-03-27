<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Modèle Table
|--------------------------------------------------------------------------
| Représente une table dans un restaurant.
| Attention : "Table" est aussi un mot réservé en SQL, mais Laravel gère ça bien.
*/

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'numero',
        'capacite',
        'emplacement',
        'disponible',
    ];

    /**
     * Relation inverse : Une table appartient à un restaurant
     * Utilisation : $table->restaurant → donne le restaurant de cette table
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Relation : Une table peut avoir plusieurs réservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Vérifie si cette table est libre à une date et heure données
     *
     * @param string $date   Format: YYYY-MM-DD
     * @param string $heure  Format: HH:MM
     * @return bool
     */
    public function estDisponible(string $date, string $heure): bool
    {
        // On cherche s'il existe une réservation active qui bloque cette table
        // On considère qu'une réservation prend 2 heures
        $debutCreneau = $heure;
        $finCreneau = date('H:i', strtotime($heure) + 7200); // +2h en secondes

        $reservationExistante = $this->reservations()
            ->where('date_reservation', $date)
            ->whereIn('statut', ['en_attente', 'confirmee']) // Ignorer les annulées
            ->where(function ($query) use ($debutCreneau, $finCreneau) {
                // Vérifie les chevauchements d'horaires
                $query->whereBetween('heure_reservation', [$debutCreneau, $finCreneau]);
            })
            ->exists(); // Retourne true si une réservation existe, false sinon

        return !$reservationExistante; // Disponible = pas de réservation existante
    }

    /**
     * Scope : filtre les tables par capacité minimale
     * Utilisation : Table::pourPersonnes(4)->get()
     */
    public function scopePourPersonnes($query, $nombrePersonnes)
    {
        if (!$nombrePersonnes) return $query;
        return $query->where('capacite', '>=', $nombrePersonnes);
    }
}
