<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Migration : Table "reservations"
|--------------------------------------------------------------------------
| Une réservation relie un client (user), une table et une date/heure.
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // Qui a fait la réservation ? (lien vers la table "users" de Laravel)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Quelle table est réservée ?
            $table->foreignId('table_id')
                  ->constrained('tables')
                  ->cascadeOnDelete();

            $table->date('date_reservation');    // Date de la réservation
            $table->time('heure_reservation');   // Heure de la réservation
            $table->integer('nombre_personnes'); // Nombre de convives

            // Statut de la réservation parmi une liste fixe
            $table->enum('statut', ['en_attente', 'confirmee', 'annulee', 'terminee'])
                  ->default('en_attente');

            $table->text('notes')->nullable();   // Notes spéciales du client (allergies, etc.)
            $table->timestamps(); // Permet de garder l'historique via created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
