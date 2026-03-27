<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Modèle Reservation
|--------------------------------------------------------------------------
| Relie un client (user), une table, une date et une heure.
*/

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'date_reservation',
        'heure_reservation',
        'nombre_personnes',
        'statut',
        'notes',
    ];

   
    protected $casts = [
        'date_reservation' => 'date', 
    ];


    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

  
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function scopeParStatut($query, $statut)
    {
        if (!$statut) return $query;
        return $query->where('statut', $statut);
    }


    public function scopeParDate($query, $date)
    {
        if (!$date) return $query;
        return $query->where('date_reservation', $date);
    }

    public function scopeParRestaurant($query, $restaurantId)
    {
        if (!$restaurantId) return $query;
        // On fait une jointure avec la table "tables" pour filtrer par restaurant
        return $query->whereHas('table', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId);
        });
    }
}
