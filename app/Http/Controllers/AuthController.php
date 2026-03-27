<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;



class AuthController extends Controller
{
   
    public function register(Request $request)
    {
        // 1. Validation des données envoyées par le client
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email', 
            'password' => 'required|string|min:6|confirmed', 
        ]);

        // 2. Créer l'utilisateur en base de données
        // Le mot de passe est hashé automatiquement (voir modèle User)
        $utilisateur = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'client', 
        ]);

        
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Compte créé avec succès !',
            'utilisateur'  => $utilisateur,
            'access_token' => $token,
            'token_type'   => 'Bearer', 
        ], 201); 
    }

    public function login(Request $request)
    {
        // 1. Validation
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Vérifier que l'email et le mot de passe sont corrects
        $utilisateur = User::where('email', $request->email)->first();

        if (!$utilisateur || !Hash::check($request->password, $utilisateur->password)) {
            // Les identifiants sont incorrects
            return response()->json([
                'message' => 'Email ou mot de passe incorrect.'
            ], 401); 
        }

        // 3. Supprimer les anciens tokens (optionnel, pour sécurité)
        $utilisateur->tokens()->delete();

        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Connexion réussie !',
            'utilisateur'  => $utilisateur,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

   
     
    public function logout(Request $request)
    {
        // Supprimer le token utilisé pour cette requête
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    public function me(Request $request)
    {
        // $request->user() retourne l'utilisateur authentifié automatiquement
        return response()->json([
            'utilisateur' => $request->user()
        ]);
    }
}
