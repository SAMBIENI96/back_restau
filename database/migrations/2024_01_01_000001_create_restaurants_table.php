<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Migration : Table "restaurants"
|--------------------------------------------------------------------------
| Une migration, c'est comme un plan pour créer une table en base de données.
| On utilise "up()" pour créer la table et "down()" pour la supprimer.
*/

return new class extends Migration
{
    /**
     * Crée la table "restaurants" en base de données
     */
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();                          
            $table->string('nom');                 
            $table->text('description')->nullable(); 
            $table->string('adresse');            
            $table->string('telephone');           
            $table->string('email')->nullable();  
            $table->string('horaires')->nullable(); 
            $table->boolean('actif')->default(true); 
            $table->timestamps(); 
        });
    }

    /**
     * Supprime la table "restaurants" (utilisé lors d'un rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
