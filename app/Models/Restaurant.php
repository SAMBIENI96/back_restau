<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'adresse',
        'telephone',
        'email',
        'horaires',
        'actif',
    ];

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

  
    public function scopeSearch($query, $terme)
    {
        // Si aucun terme de recherche, on retourne la requête sans filtre
        if (!$terme) return $query;

        return $query->where('nom', 'like', "%{$terme}%")
                     ->orWhere('adresse', 'like', "%{$terme}%");
    }

    /*
      Scope : seulement les restaurants actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }
}
