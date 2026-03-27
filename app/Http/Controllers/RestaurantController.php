<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| RestaurantController
|--------------------------------------------------------------------------
| Gère les opérations CRUD sur les restaurants.
| CRUD = Create (créer), Read (lire), Update (modifier), Delete (supprimer)
*/

class RestaurantController extends Controller
{
    /**
     * GET /api/restaurants
     * Liste tous les restaurants avec filtres et pagination
     */
    public function index(Request $request)
    {
        // On commence une requête qu'on va construire progressivement
        $query = Restaurant::query();

        // --- Filtres optionnels ---

        // Recherche par nom ou adresse : ?search=Paris
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filtrer seulement les actifs : ?actif=true
        if ($request->boolean('actif')) {
            $query->actifs();
        }

        // --- Tri ---
        // ?tri=nom ou ?tri=created_at
        $tri = $request->get('tri', 'created_at'); // Par défaut, tri par date de création
        $ordre = $request->get('ordre', 'desc');   // Par défaut, du plus récent au plus ancien
        $query->orderBy($tri, $ordre);

        // --- Pagination ---
        // ?par_page=10 → 10 résultats par page
        $parPage = $request->get('par_page', 15); // 15 par défaut
        $restaurants = $query->paginate($parPage);

        return response()->json($restaurants);
    }

    /**
     * POST /api/restaurants
     * Créer un nouveau restaurant (admin seulement)
     */
    public function store(Request $request)
    {
        // Vérifier que l'utilisateur est administrateur
        if (!$request->user()->estAdmin()) {
            return response()->json([
                'message' => 'Accès refusé. Seuls les administrateurs peuvent créer des restaurants.'
            ], 403); // 403 = Forbidden
        }

        // Validation des données
        $donnees = $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'adresse'     => 'required|string|max:255',
            'telephone'   => 'required|string|max:20',
            'email'       => 'nullable|email|max:255',
            'horaires'    => 'nullable|string|max:255',
            'actif'       => 'boolean',
        ]);

        // Créer le restaurant avec les données validées
        $restaurant = Restaurant::create($donnees);

        return response()->json([
            'message'    => 'Restaurant créé avec succès !',
            'restaurant' => $restaurant,
        ], 201);
    }

    /**
     * GET /api/restaurants/{id}
     * Voir un restaurant avec ses tables
     */
    public function show(Restaurant $restaurant)
    {
        // "Route Model Binding" : Laravel trouve automatiquement le restaurant par son ID
        // On charge aussi les tables associées avec "with()"
        $restaurant->load('tables');

        return response()->json([
            'restaurant' => $restaurant
        ]);
    }

    /**
     * PUT /api/restaurants/{id}
     * Modifier un restaurant (admin seulement)
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        // Vérifier les droits
        if (!$request->user()->estAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Validation (tous les champs sont optionnels avec "sometimes")
        $donnees = $request->validate([
            'nom'         => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'adresse'     => 'sometimes|string|max:255',
            'telephone'   => 'sometimes|string|max:20',
            'email'       => 'nullable|email|max:255',
            'horaires'    => 'nullable|string|max:255',
            'actif'       => 'boolean',
        ]);

        // Mettre à jour le restaurant
        $restaurant->update($donnees);

        return response()->json([
            'message'    => 'Restaurant mis à jour.',
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * DELETE /api/restaurants/{id}
     * Supprimer un restaurant (admin seulement)
     */
    public function destroy(Request $request, Restaurant $restaurant)
    {
        if (!$request->user()->estAdmin()) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $restaurant->delete();

        return response()->json([
            'message' => 'Restaurant supprimé avec succès.'
        ]);
    }
}
