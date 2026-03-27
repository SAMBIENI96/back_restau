<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes - Toutes les routes de notre application
|--------------------------------------------------------------------------
|
| Ici on définit toutes les URLs disponibles dans l'API.
| Les routes publiques ne nécessitent pas de connexion.
| Les routes protégées nécessitent un token (auth:sanctum).
|
*/

// =============================================
// ROUTES PUBLIQUES (pas besoin d'être connecté)
// =============================================

// Connexion et inscription
Route::post('/register', [AuthController::class, 'register']); // Créer un compte
Route::post('/login',    [AuthController::class, 'login']);    // Se connecter

// =============================================
// ROUTES PROTÉGÉES (il faut être connecté)
// =============================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Authentification ---
    Route::post('/logout', [AuthController::class, 'logout']); // Se déconnecter
    Route::get('/me',      [AuthController::class, 'me']);     // Voir son propre profil

    // --- Restaurants ---
    // GET    /restaurants        → liste tous les restaurants (avec filtres et pagination)
    // POST   /restaurants        → créer un restaurant (admin seulement)
    // GET    /restaurants/{id}   → voir un restaurant
    // PUT    /restaurants/{id}   → modifier un restaurant (admin seulement)
    // DELETE /restaurants/{id}   → supprimer un restaurant (admin seulement)
    Route::apiResource('restaurants', RestaurantController::class);

    // --- Tables d'un restaurant ---
    // GET    /restaurants/{id}/tables       → liste les tables du restaurant
    // POST   /restaurants/{id}/tables       → ajouter une table (admin)
    // GET    /restaurants/{id}/tables/{id}  → voir une table
    // PUT    /restaurants/{id}/tables/{id}  → modifier une table (admin)
    // DELETE /restaurants/{id}/tables/{id} → supprimer une table (admin)
    Route::apiResource('restaurants.tables', TableController::class);

    // Route spéciale : voir les tables disponibles pour une date/heure donnée
    // Exemple : GET /restaurants/1/tables/disponibles?date=2025-01-15&heure=19:00&personnes=4
    Route::get('restaurants/{restaurant}/tables-disponibles', [TableController::class, 'disponibles']);

    // --- Réservations ---
    // GET    /reservations        → liste les réservations (filtrées selon le rôle)
    // POST   /reservations        → créer une réservation
    // GET    /reservations/{id}   → voir une réservation
    // PUT    /reservations/{id}   → modifier une réservation
    // DELETE /reservations/{id}   → annuler une réservation
    Route::apiResource('reservations', ReservationController::class);

    // Route spéciale : voir l'historique des réservations de l'utilisateur connecté
    Route::get('/mes-reservations', [ReservationController::class, 'mesReservations']);
});
