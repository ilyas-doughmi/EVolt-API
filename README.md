# ⚡ EVolt API

API REST Laravel pour la gestion des bornes de recharge pour véhicules électriques.

![Laravel](https://img.shields.io/badge/Laravel-11-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## Démo

**API Production:** [https://evolt-d25574.dockhosting.dev/api](https://evolt-d25574.dockhosting.dev/api)

---

## Description

EVolt API permet aux utilisateurs de :
- Rechercher des bornes de recharge disponibles avec géolocalisation
- Réserver un créneau de recharge
- Modifier ou annuler leurs réservations
- Consulter l'historique de leurs sessions de recharge

Les administrateurs peuvent :
- Ajouter, modifier ou supprimer des bornes
- Gérer les types de connecteurs et puissances disponibles

---

## Fonctionnalités

| Fonctionnalité | Description |
|----------------|-------------|
| Authentification | Système complet avec Laravel Sanctum (tokens API) |
| Gestion des rôles | Rôles utilisateur et administrateur |
| Bornes de recharge | CRUD complet avec géolocalisation, type de connecteur et puissance |
| Réservations | Création, modification et annulation de réservations |
| Historique | Consultation des sessions passées et en cours |

---

## Technologies

- **Framework:** Laravel 11
- **PHP:** 8.2+
- **Authentification:** Laravel Sanctum
- **Base de données:** MySQL / SQLite
- **Tests:** PHPUnit

---

## Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL ou SQLite

### Étapes d'installation

```bash
# 1. Cloner le repository
git clone https://github.com/ilyas-doughmi/EVolt-API.git
cd EVolt-API/web

# 2. Installer les dépendances
composer install

# 3. Copier le fichier d'environnement
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=evolt
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Exécuter les migrations
php artisan migrate

# 7. (Optionnel) Exécuter les seeders
php artisan db:seed

# 8. Lancer le serveur de développement
php artisan serve
```

L'API sera accessible sur `http://localhost:8000/api`

---

## Documentation API

### Base URL

```
Production: https://evolt-d25574.dockhosting.dev/api
Local: http://localhost:8000/api
```

### Authentification

L'API utilise **Laravel Sanctum** avec des tokens Bearer. Incluez le token dans l'en-tête de chaque requête protégée :

```
Authorization: Bearer {votre_token}
```

---

### Endpoints

#### Auth

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| `POST` | `/auth/register` | Créer un compte utilisateur | Non |
| `POST` | `/auth/login` | Se connecter | Non |
| `POST` | `/auth/logout` | Se déconnecter | Oui |

##### POST /auth/register

Créer un nouveau compte utilisateur.

**Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

**Réponse (201):**
```json
{
    "message": "Utilisateur créé avec succès",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    },
    "token": "1|abc123..."
}
```

##### POST /auth/login

Authentifier un utilisateur.

**Body (JSON):**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Réponse (200):**
```json
{
    "message": "Connexion réussie",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user"
    },
    "token": "2|xyz789..."
}
```

##### POST /auth/logout

Déconnecter l'utilisateur (révoque le token).

**Headers:**
```
Authorization: Bearer {token}
```

**Réponse (200):**
```json
{
    "message": "Déconnexion réussie"
}
```

---

#### Stations

| Méthode | Endpoint | Description | Auth | Rôle |
|---------|----------|-------------|------|------|
| `GET` | `/stations` | Lister toutes les bornes | Oui | Tous |
| `POST` | `/stations` | Créer une borne | Oui | Admin |
| `PUT` | `/stations/{id}` | Modifier une borne | Oui | Admin |
| `DELETE` | `/stations/{id}` | Supprimer une borne | Oui | Admin |

##### GET /stations

Récupérer la liste des bornes de recharge.

**Réponse (200):**
```json
[
    {
        "id": 1,
        "name": "Borne Paris Centre",
        "latitude": 48.8566,
        "longitude": 2.3522,
        "status": "available",
        "connector_type": "Type 2",
        "power_kw": 22.0,
        "created_at": "2026-03-09T10:00:00.000000Z",
        "updated_at": "2026-03-09T10:00:00.000000Z"
    }
]
```

##### POST /stations (Admin)

Créer une nouvelle borne de recharge.

**Body (JSON):**
```json
{
    "name": "Borne Lyon Part-Dieu",
    "latitude": 45.7640,
    "longitude": 4.8357,
    "connector_type": "CCS",
    "power_kw": 50.0,
    "status": "available"
}
```

**Réponse (201):**
```json
{
    "id": 2,
    "name": "Borne Lyon Part-Dieu",
    "latitude": 45.7640,
    "longitude": 4.8357,
    "status": "available",
    "connector_type": "CCS",
    "power_kw": 50.0,
    "created_at": "2026-03-10T12:00:00.000000Z",
    "updated_at": "2026-03-10T12:00:00.000000Z"
}
```

##### PUT /stations/{id} (Admin)

Modifier une borne existante.

**Body (JSON):**
```json
{
    "name": "Borne Lyon Part-Dieu (Mise à jour)",
    "status": "maintenance",
    "power_kw": 75.0
}
```

##### DELETE /stations/{id} (Admin)

Supprimer une borne.

**Réponse (200):**
```json
{
    "message": "Station supprimée avec succès"
}
```

---

#### Réservations

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| `GET` | `/reservations` | Lister mes réservations | Oui |
| `POST` | `/reservations` | Créer une réservation | Oui |
| `PUT` | `/reservations/{id}` | Modifier ma réservation | Oui |
| `DELETE` | `/reservations/{id}` | Annuler ma réservation | Oui |

##### GET /reservations

Récupérer l'historique des réservations de l'utilisateur.

**Réponse (200):**
```json
[
    {
        "id": 1,
        "user_id": 1,
        "station_id": 1,
        "start_time": "2026-03-10T14:00:00.000000Z",
        "duration_minutes": 60,
        "end_time": "2026-03-10T15:00:00.000000Z",
        "status": "pending",
        "energy_delivered_kwh": null,
        "station": {
            "id": 1,
            "name": "Borne Paris Centre",
            "connector_type": "Type 2",
            "power_kw": 22.0
        }
    }
]
```

##### POST /reservations

Créer une nouvelle réservation.

**Body (JSON):**
```json
{
    "station_id": 1,
    "start_time": "2026-03-11T10:00:00",
    "duration_minutes": 45
}
```

**Réponse (201):**
```json
{
    "id": 2,
    "user_id": 1,
    "station_id": 1,
    "start_time": "2026-03-11T10:00:00.000000Z",
    "duration_minutes": 45,
    "end_time": "2026-03-11T10:45:00.000000Z",
    "status": "pending"
}
```

##### PUT /reservations/{id}

Modifier une réservation existante (utilisateur propriétaire uniquement).

**Body (JSON):**
```json
{
    "start_time": "2026-03-11T11:00:00",
    "duration_minutes": 60
}
```

##### DELETE /reservations/{id}

Annuler une réservation (utilisateur propriétaire uniquement).

**Réponse (200):**
```json
{
    "message": "Réservation annulée avec succès"
}
```

---

## Schéma de Base de Données

### Users
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| name | string | Nom de l'utilisateur |
| email | string | Email unique |
| password | string | Mot de passe hashé |
| role | enum | 'user' ou 'admin' |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de mise à jour |

### Stations
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| name | string | Nom de la borne |
| latitude | decimal(10,8) | Latitude GPS |
| longitude | decimal(11,8) | Longitude GPS |
| status | enum | 'available', 'occupied', 'maintenance' |
| connector_type | string | Type de connecteur (Type 2, CCS, CHAdeMO...) |
| power_kw | float | Puissance en kW |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de mise à jour |

### Reservations
| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| user_id | bigint | Référence utilisateur |
| station_id | bigint | Référence station |
| start_time | datetime | Heure de début |
| duration_minutes | integer | Durée en minutes (min: 15) |
| end_time | datetime | Heure de fin (calculée) |
| status | enum | 'pending', 'active', 'completed', 'cancelled' |
| energy_delivered_kwh | float | Énergie délivrée (nullable) |
| created_at | timestamp | Date de création |
| updated_at | timestamp | Date de mise à jour |

---

## Tests

Exécuter les tests unitaires :

```bash
php artisan test
```

Ou avec PHPUnit directement :

```bash
./vendor/bin/phpunit
```

---

## Codes de Réponse HTTP

| Code | Description |
|------|-------------|
| 200 | Succès |
| 201 | Ressource créée |
| 400 | Requête invalide |
| 401 | Non authentifié |
| 403 | Accès refusé (permissions insuffisantes) |
| 404 | Ressource non trouvée |
| 422 | Erreur de validation |
| 500 | Erreur serveur |

---

## Variables d'Environnement

| Variable | Description | Exemple |
|----------|-------------|---------|
| `APP_NAME` | Nom de l'application | EVolt |
| `APP_ENV` | Environnement | local / production |
| `APP_DEBUG` | Mode debug | true / false |
| `DB_CONNECTION` | Driver BDD | mysql / sqlite |
| `DB_HOST` | Hôte BDD | 127.0.0.1 |
| `DB_DATABASE` | Nom de la BDD | evolt |
| `DB_USERNAME` | Utilisateur BDD | root |
| `DB_PASSWORD` | Mot de passe BDD | secret |

---

## Auteurs

- **Ilyas Doughmi** - [GitHub](https://github.com/ilyas-doughmi)

---

## Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.


