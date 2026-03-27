<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

/*
|--------------------------------------------------------------------------
| DatabaseSeeder - Remplir la base avec des données de test béninoises
|--------------------------------------------------------------------------
| Lance avec : php artisan db:seed
| Ou avec les migrations : php artisan migrate --seed
*/

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Créer des utilisateurs de test ---

        // Compte administrateur
        $admin = User::create([
            'name'     => 'Koffi Adjovi',
            'email'    => 'admin@resto-benin.com',
            'password' => 'password123', // Hashé automatiquement
            'role'     => 'admin',
        ]);

        // Clients de test
        $client1 = User::create([
            'name'     => 'Ebenezer Dossou',
            'email'    => 'ebenezer@example.com',
            'password' => 'password123',
            'role'     => 'client',
        ]);

        $client2 = User::create([
            'name'     => 'Aïcha Bio Tchané',
            'email'    => 'aicha@example.com',
            'password' => 'password123',
            'role'     => 'client',
        ]);

        // --- Créer des restaurants de test ---

        $resto1 = Restaurant::create([
            'nom'         => 'La Terrasse de Cotonou',
            'description' => 'Restaurant traditionnel béninois avec spécialités de sauce arachide, akassa et poisson braisé.',
            'adresse'     => 'Quartier Cadjehoun, Avenue Jean-Paul II, Cotonou',
            'telephone'   => '+229 97 12 34 56',
            'email'       => 'contact@terrasse-cotonou.bj',
            'horaires'    => 'Mar-Dim : 11h-15h et 18h-22h30',
            'actif'       => true,
        ]);

        $resto2 = Restaurant::create([
            'nom'         => 'Chez Maman Ouidah',
            'description' => 'Cuisine béninoise authentique, spécialités de Ouidah : hounhouè, téképè et crabe sauce tomate.',
            'adresse'     => 'Route de la Plage, Ouidah',
            'telephone'   => '+229 96 78 90 11',
            'email'       => 'mamanouidah@gmail.com',
            'horaires'    => 'Lun-Sam : 10h-23h',
            'actif'       => true,
        ]);

        // --- Créer des tables pour chaque restaurant ---

        // Tables de La Terrasse de Cotonou
        $table1 = Table::create([
            'restaurant_id' => $resto1->id,
            'numero'        => 'Table 1',
            'capacite'      => 2,
            'emplacement'   => 'Intérieur climatisé',
            'disponible'    => true,
        ]);

        $table2 = Table::create([
            'restaurant_id' => $resto1->id,
            'numero'        => 'Table 2',
            'capacite'      => 4,
            'emplacement'   => 'Terrasse extérieure',
            'disponible'    => true,
        ]);

        $table3 = Table::create([
            'restaurant_id' => $resto1->id,
            'numero'        => 'Table VIP',
            'capacite'      => 8,
            'emplacement'   => 'Salon privé',
            'disponible'    => true,
        ]);

        // Tables de Chez Maman Ouidah
        Table::create([
            'restaurant_id' => $resto2->id,
            'numero'        => 'Table A',
            'capacite'      => 2,
            'emplacement'   => 'Vue sur jardin',
            'disponible'    => true,
        ]);

        Table::create([
            'restaurant_id' => $resto2->id,
            'numero'        => 'Table B',
            'capacite'      => 6,
            'emplacement'   => 'Grande salle',
            'disponible'    => true,
        ]);

        // --- Créer quelques réservations de test ---

        Reservation::create([
            'user_id'           => $client1->id,
            'table_id'          => $table2->id,
            'date_reservation'  => now()->addDays(3)->format('Y-m-d'), // Dans 3 jours
            'heure_reservation' => '19:30',
            'nombre_personnes'  => 3,
            'statut'            => 'confirmee',
            'notes'             => 'Baptême de bébé, merci de prévoir une grande nappe.',
        ]);

        Reservation::create([
            'user_id'           => $client2->id,
            'table_id'          => $table1->id,
            'date_reservation'  => now()->addDays(7)->format('Y-m-d'),
            'heure_reservation' => '12:00',
            'nombre_personnes'  => 2,
            'statut'            => 'en_attente',
            'notes'             => 'Pas de piment svp.',
        ]);

        // Ancienne réservation (historique)
        Reservation::create([
            'user_id'           => $client1->id,
            'table_id'          => $table3->id,
            'date_reservation'  => now()->subDays(10)->format('Y-m-d'), // Il y a 10 jours
            'heure_reservation' => '20:00',
            'nombre_personnes'  => 6,
            'statut'            => 'terminee',
            'notes'             => 'Réunion de famille, menu complet avec boissons.',
        ]);

        // Afficher les infos de connexion dans le terminal
        $this->command->info('✅ Base de données remplie avec succès !');
        $this->command->info('Admin    : admin@resto-benin.com / password123');
        $this->command->info('Client 1 : ebenezer@example.com / password123');
        $this->command->info('Client 2 : aicha@example.com / password123');
    }
}