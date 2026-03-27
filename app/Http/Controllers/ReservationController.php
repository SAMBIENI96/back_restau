<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;



class ReservationController extends Controller
{
    /**
     * GET /api/reservations
     * Liste les réservations (filtrées selon le rôle de l'utilisateur)
     */
    public function index(Request $request)
    {
        $utilisateur = $request->user();

        $query = Reservation::with(['client', 'table.restaurant']);

        if ($utilisateur->estAdmin()) {
            // L'admin peut voir TOUTES les réservations et filtrer par client
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        } else {
            // Un client normal ne voit QUE ses propres réservations
            $query->where('user_id', $utilisateur->id);
        }

      
        $query->parStatut($request->statut);

        $query->parDate($request->date);

        $query->parRestaurant($request->restaurant_id);

        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('date_reservation', [
                $request->date_debut,
                $request->date_fin
            ]);
        }

        $query->orderBy('date_reservation', $request->get('ordre', 'desc'));

        // --- Pagination ---
        $parPage = $request->get('par_page', 15);
        $reservations = $query->paginate($parPage);

        return response()->json($reservations);
    }

   
    public function store(Request $request)
    {
        // Validation des données
        $donnees = $request->validate([
            'table_id'          => 'required|exists:tables,id', 
            'date_reservation'  => 'required|date|after_or_equal:today',
            'heure_reservation' => 'required|date_format:H:i',
            'nombre_personnes'  => 'required|integer|min:1',
            'notes'             => 'nullable|string|max:500',
        ]);

        $table = Table::findOrFail($donnees['table_id']);

        // Vérifier que la capacité est suffisante
        if ($donnees['nombre_personnes'] > $table->capacite) {
            return response()->json([
                'message' => "Cette table ne peut accueillir que {$table->capacite} personnes maximum."
            ], 422); 
        }

        // Vérifier que la table est disponible à cette date/heure
        if (!$table->estDisponible($donnees['date_reservation'], $donnees['heure_reservation'])) {
            return response()->json([
                'message' => 'Cette table est déjà réservée pour cette date et cette heure.'
            ], 422);
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id'           => $request->user()->id, 
            'table_id'          => $donnees['table_id'],
            'date_reservation'  => $donnees['date_reservation'],
            'heure_reservation' => $donnees['heure_reservation'],
            'nombre_personnes'  => $donnees['nombre_personnes'],
            'notes'             => $donnees['notes'] ?? null,
            'statut'            => 'en_attente', 
        ]);

        $reservation->load(['client', 'table.restaurant']);

        return response()->json([
            'message'     => 'Réservation créée avec succès !',
            'reservation' => $reservation,
        ], 201);
    }

    /* Voir une réservation en détail
     */
    public function show(Request $request, Reservation $reservation)
    {
        // Vérifier les droits : le client ne peut voir que ses réservations
        if (!$request->user()->estAdmin() && $reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $reservation->load(['client', 'table.restaurant']);

        return response()->json(['reservation' => $reservation]);
    }

 
    public function update(Request $request, Reservation $reservation)
    {
        $utilisateur = $request->user();

        // Un client peut modifier uniquement ses propres réservations
        if (!$utilisateur->estAdmin() && $reservation->user_id !== $utilisateur->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if (in_array($reservation->statut, ['annulee', 'terminee'])) {
            return response()->json([
                'message' => 'Impossible de modifier une réservation annulée ou terminée.'
            ], 422);
        }

        // Les clients peuvent modifier date/heure/personnes/notes
        // Les admins peuvent aussi changer le statut
        $reglesValidation = [
            'date_reservation'  => 'sometimes|date|after_or_equal:today',
            'heure_reservation' => 'sometimes|date_format:H:i',
            'nombre_personnes'  => 'sometimes|integer|min:1',
            'notes'             => 'nullable|string|max:500',
        ];

        if ($utilisateur->estAdmin()) {
            $reglesValidation['statut'] = 'sometimes|in:en_attente,confirmee,annulee,terminee';
            $reglesValidation['table_id'] = 'sometimes|exists:tables,id';
        }

        $donnees = $request->validate($reglesValidation);

        $tableId = $donnees['table_id'] ?? $reservation->table_id;
        $date    = $donnees['date_reservation'] ?? $reservation->date_reservation->format('Y-m-d');
        $heure   = $donnees['heure_reservation'] ?? $reservation->heure_reservation;

        if (isset($donnees['date_reservation']) || isset($donnees['heure_reservation']) || isset($donnees['table_id'])) {
            $table = Table::find($tableId);

            $autreReservation = $table->reservations()
                ->where('date_reservation', $date)
                ->whereIn('statut', ['en_attente', 'confirmee'])
                ->where('id', '!=', $reservation->id)
                ->exists();

            if ($autreReservation) {
                return response()->json([
                    'message' => 'Cette table est déjà réservée pour cette date et heure.'
                ], 422);
            }
        }

        $reservation->update($donnees);
        $reservation->load(['client', 'table.restaurant']);

        return response()->json([
            'message'     => 'Réservation mise à jour.',
            'reservation' => $reservation,
        ]);
    }

    // Annuler une réservation
     
    public function destroy(Request $request, Reservation $reservation)
    {
        $utilisateur = $request->user();

        if (!$utilisateur->estAdmin() && $reservation->user_id !== $utilisateur->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // On ne supprime pas vraiment, on change le statut à "annulee"
        $reservation->update(['statut' => 'annulee']);

        return response()->json([
            'message' => 'Réservation annulée avec succès.'
        ]);
    }

    public function mesReservations(Request $request)
    {
        $utilisateur = $request->user();

        $reservations = Reservation::with(['table.restaurant'])
            ->where('user_id', $utilisateur->id)
            ->orderBy('date_reservation', 'desc')
            ->paginate($request->get('par_page', 10));

        return response()->json([
            'utilisateur'  => $utilisateur->name,
            'reservations' => $reservations,
        ]);
    }
}
