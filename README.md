# 🍽️ API Réservation de Restaurants —

Backend complet d'une application de réservation de tables dans des restaurants, développé avec **Laravel 11** et **Laravel Sanctum**.

---

## 📋 Fonctionnalités

- ✅ Gestion des restaurants (création, consultation, mise à jour)
- ✅ Gestion des tables associées à un restaurant
- ✅ Définition de la capacité des tables
- ✅ Consultation des tables disponibles selon date, heure et nombre de personnes
- ✅ Effectuer une réservation
- ✅ Consulter les réservations
- ✅ Modifier ou annuler une réservation
- ✅ Vérification de disponibilité d'une table selon une date et une heure
- ✅ Association d'une réservation à un client
- ✅ Authentification des utilisateurs (Laravel Sanctum)
- ✅ Gestion des rôles (client, administrateur)
- ✅ Historique des réservations
- ✅ Recherche et filtrage (restaurant, date, capacité…)
- ✅ Pagination des résultats
- 🎁 Interface frontend de test incluse

---

## 🚀 Installation

### Prérequis

- PHP 8.1 ou supérieur
- Composer
- SQLite (inclus avec PHP) ou MySQL

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/SAMBIENI96/back_restau.git
cd back_restau

# 2. Installer les dépendances
composer install

# 3. Copier le fichier de configuration
cp .env.example .env

# 4. Générer la clé de l'application
php artisan key:generate

# 5. Créer la base de données SQLite
touch database/database.sqlite

# 6. Lancer les migrations et remplir la base
php artisan migrate --seed

# 7. Démarrer le serveur
php artisan serve
```

L'API est accessible sur : **http://127.0.0.1:8000/api**

---

## 🧪 Comptes de test

| Rôle   | Email                 | Mot de passe |
| ------ | --------------------- | ------------ |
| Admin  | admin@resto-benin.com | password123  |
| Client | ebenezer@example.com  | password123  |
| Client | aicha@example.com     | password123  |

---

## 📌 Endpoints disponibles

### Authentification

| Méthode | URL           | Description     | Auth |
| ------- | ------------- | --------------- | ---- |
| POST    | /api/register | Créer un compte | ❌   |
| POST    | /api/login    | Se connecter    | ❌   |
| POST    | /api/logout   | Se déconnecter  | ✅   |
| GET     | /api/me       | Mon profil      | ✅   |

### Restaurants

| Méthode | URL                   | Description             | Rôle  |
| ------- | --------------------- | ----------------------- | ----- |
| GET     | /api/restaurants      | Liste avec filtres      | Tous  |
| POST    | /api/restaurants      | Créer un restaurant     | Admin |
| GET     | /api/restaurants/{id} | Voir un restaurant      | Tous  |
| PUT     | /api/restaurants/{id} | Modifier un restaurant  | Admin |
| DELETE  | /api/restaurants/{id} | Supprimer un restaurant | Admin |

### Tables

| Méthode | URL                                      | Description         | Rôle  |
| ------- | ---------------------------------------- | ------------------- | ----- |
| GET     | /api/restaurants/{id}/tables             | Liste des tables    | Tous  |
| POST    | /api/restaurants/{id}/tables             | Ajouter une table   | Admin |
| GET     | /api/restaurants/{id}/tables/{tableId}   | Voir une table      | Tous  |
| PUT     | /api/restaurants/{id}/tables/{tableId}   | Modifier une table  | Admin |
| DELETE  | /api/restaurants/{id}/tables/{tableId}   | Supprimer une table | Admin |
| GET     | /api/restaurants/{id}/tables-disponibles | Tables disponibles  | Tous  |

### Réservations

| Méthode | URL                    | Description              | Rôle |
| ------- | ---------------------- | ------------------------ | ---- |
| GET     | /api/reservations      | Liste avec filtres       | Tous |
| POST    | /api/reservations      | Créer une réservation    | Tous |
| GET     | /api/reservations/{id} | Voir une réservation     | Tous |
| PUT     | /api/reservations/{id} | Modifier une réservation | Tous |
| DELETE  | /api/reservations/{id} | Annuler une réservation  | Tous |
| GET     | /api/mes-reservations  | Mon historique           | Tous |

---

## 🎨 Interface de test

Un testeur frontend est inclus dans `public/testeur-api.html`.

Une fois le serveur lancé, accède-y via :

```
http://127.0.0.1:8000/testeur-api.html
```

---

## 🗂️ Structure du projet

```
app/
  Models/           → User, Restaurant, Table, Reservation
  Http/Controllers/ → AuthController, RestaurantController, TableController, ReservationController
database/
  migrations/       → Création des tables en base de données
  seeders/          → Données de test béninoises
routes/
  api.php           → Toutes les routes de l'API
public/
  testeur-api.html  → Interface de test frontend
swagger.yaml        → Documentation Swagger de l'API
```

---

## 🛠️ Technologies utilisées

- **Laravel 11** — Framework PHP
- **Laravel Sanctum** — Authentification par token
- **SQLite** — Base de données légère
- **Swagger / OpenAPI 3.0** — Documentation API

---

## 👨‍💻 Auteur

Projet réalisé dans le cadre d'un défi de développement backend.
