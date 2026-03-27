<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;



class TableController extends Controller
{
    /**
     * Liste les tables d'un restaurant
     */
    public function index(Request $request, Restaurant $restaurant)
    {

        if ($request->has('personnes')) {
            $query->pourPersonnes($request->personnes);
        }

        if ($request->has('emplacement')) {
            $query->where('emplacement', $request->emplacement);
        }

        if ($request->has('disponible')) {
            $query->where('disponible', $request->boolean('disponible'));
        }

        $parPage = $request->get('par_page', 10);
        $tables = $query->paginate($parPage);

        return response()->json($tables);
    }

    /**
     * POST /api/restaurants/{restaurant}/tables
     * Ajouter une table à un restaurant (admin seulement)
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        if (!$request->user()->estAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $donnees = $request->validate([
            'numero'      => 'required|string|max:50',
            'capacite'    => 'required|integer|min:1|max:50',
            'emplacement' => 'nullable|string|max:100',
            'disponible'  => 'boolean',
        ]);

        // On associe la table au restaurant via son ID
        $donnees['restaurant_id'] = $restaurant->id;

        $table = Table::create($donnees);

        return response()->json([
            'message' => 'Table ajoutée avec succès !',
            'table'   => $table,
        ], 201);
    }

    /**
     * GET /api/restaurants/{restaurant}/tables/{table}
     * Voir une table précise
     */
    public function show(Restaurant $restaurant, Table $table)
    {
        // Vérifier que cette table appartient bien à ce restaurant
        if ($table->restaurant_id !== $restaurant->id) {
            return response()->json(['message' => 'Table introuvable dans ce restaurant.'], 404);
        }

        $table->load('restaurant');

        return response()->json(['table' => $table]);
    }

    /**
     * Modifier une table (admin seulement)
     */
    public function update(Request $request, Restaurant $restaurant, Table $table)
    {
        if (!$request->user()->estAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($table->restaurant_id !== $restaurant->id) {
            return response()->json(['message' => 'Table introuvable dans ce restaurant.'], 404);
        }

        $donnees = $request->validate([
            'numero'      => 'sometimes|string|max:50',
            'capacite'    => 'sometimes|integer|min:1|max:50',
            'emplacement' => 'nullable|string|max:100',
            'disponible'  => 'boolean',
        ]);

        $table->update($donnees);

        return response()->json([
            'message' => 'Table mise à jour.',
            'table'   => $table,
        ]);
    }

    /**
     * DELETE /api/restaurants/{restaurant}/tables/{table}
     * Supprimer une table (admin seulement)
     */
    public function destroy(Request $request, Restaurant $restaurant, Table $table)
    {
        if (!$request->user()->estAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($table->restaurant_id !== $restaurant->id) {
            return response()->json(['message' => 'Table introuvable.'], 404);
        }

        $table->delete();

        return response()->json(['message' => 'Table supprimée.']);
    }

    public function disponibles(Request $request, Restaurant $restaurant)
    {
        // Validation des paramètres de recherche
        $request->validate([
            'date'      => 'required|date|after_or_equal:today', // Pas dans le passé
            'heure'     => 'required|date_format:H:i',
            'personnes' => 'required|integer|min:1',
        ]);

        // Récupérer toutes les tables actives du restaurant avec assez de capacité
        $tables = $restaurant->tables()
            ->where('disponible', true)
            ->pourPersonnes($request->personnes)
            ->get();

        $tablesDisponibles = $tables->filter(function ($table) use ($request) {
            return $table->estDisponible($request->date, $request->heure);
        });

        return response()->json([
            'date'              => $request->date,
            'heure'             => $request->heure,
            'personnes'         => $request->personnes,
            'tables_disponibles' => $tablesDisponibles->values(), // values() réindexe le tableau
        ]);
    }
}
