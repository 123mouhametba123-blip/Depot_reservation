# Réservation de salles universitaires — Reservation_mouhamed

Application web (projet ODC-P8) de consultation des salles et de gestion de
leurs réservations. Développée en **PHP orienté objet**, sans framework
complet, à partir de composants spécialisés installés avec Composer.

- **Routeur** : `nikic/fast-route`
- **Validation** : `respect/validation`
- **ORM** : `illuminate/database` (Eloquent via `Capsule\Manager`)
- **Conteneur d'injection** : `php-di/php-di`
- **Variables d'environnement** : `vlucas/phpdotenv`

## Fonctionnalités

### Gestion des salles
- consulter la liste des salles ;
- afficher le détail d'une salle ;
- ajouter une salle ;
- modifier une salle ;
- activer / désactiver une salle.

`Salle(id, nom, batiment, capacite, type, active, created_at, updated_at)`
Types autorisés : `cours | informatique | laboratoire | amphitheatre | reunion`

### Gestion des réservations
- consulter toutes les réservations ;
- filtrer les réservations par salle ;
- afficher une réservation ;
- créer une réservation ;
- annuler une réservation.

`Reservation(id, salle_id, responsable, email, motif, date_debut, date_fin, statut, created_at, updated_at)`
Statuts autorisés : `confirmee | annulee`

### Règles métier
Une réservation est acceptée uniquement si :
1. la salle existe ;
2. la salle est active ;
3. le nom du responsable est renseigné ;
4. l'adresse électronique est valide ;
5. le motif contient entre 5 et 255 caractères ;
6. la date de début précède la date de fin ;
7. la réservation dure au maximum quatre heures ;
8. la réservation commence dans le futur ;
9. aucune réservation confirmée ne chevauche cette période.

Deux réservations se chevauchent lorsque :
`nouveauDebut < reservationExistante.dateFin ET nouvelleFin > reservationExistante.dateDebut`.
Une réservation annulée ne bloque plus la salle.

## Prérequis

- PHP **8.2 ou 8.3** avec les extensions `pdo_mysql`, `mbstring` ;
- Composer 2 ;
- MySQL 5.7 / 8.0 (ou MariaDB) ;
- **ou** Docker (image `php:8.3-cli` avec le serveur intégré PHP + `mysql:8.0`)
  pour un démarrage sans installation locale.

## Installation sans Docker

```bash
# 1. Cloner le projet
git clone https://github.com/<vous>/Reservation_mouhamed.git
cd Reservation_mouhamed

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
#     - adapter DB_DATABASE, DB_USERNAME, DB_PASSWORD si nécessaire
#     - le fichier .env n'est PAS versionné (secrets)

# 4. Créer les tables (migrations Eloquent)
composer db:migrate

# 5. Ajouter les salles initiales (idempotent)
composer db:seed

# 6. Lancer le serveur intégré de PHP
php -S 127.0.0.1:8000 -t public public/index.php
```

Puis ouvrir **http://127.0.0.1:8000**.

## Installation avec Docker

```bash
cp .env.example .env
docker compose up -d --build
```

Puis ouvrir **http://localhost:8080** (application) et **http://localhost:8081**
non utilisé : la base MySQL est exposée sur le port hôte **3307** (3306 peut être
occupé par un MySQL local).

L'entrypoint attend que MySQL soit sain, applique les migrations et insère les
données initiales avant de démarrer le **serveur PHP intégré** (`php -S`) — aucun
`docker exec` nécessaire.

```bash
docker compose ps         # état des conteneurs
docker compose logs -f app
docker compose down       # arrêt (les données persistent dans le volume db_data)
```

## Exécution des tests

Les tests unitaires (services, validateurs) utilisent des **doublures en
mémoire** des repositories : ils ne nécessitent **pas** MySQL.

```bash
composer test
# ou
vendor/bin/phpunit
```

Les tests d'intégration (Eloquent) utilisent automatiquement :
- SQLite en mémoire si `pdo_sqlite` est disponible, sinon ;
- une base MySQL dédiée (variables `DB_TEST_*`, créée automatiquement).

```bash
DB_TEST_HOST=127.0.0.1 DB_TEST_PORT=3307 \
DB_TEST_USERNAME=root DB_TEST_PASSWORD=root \
DB_TEST_DATABASE=reservation_salles_tests \
vendor/bin/phpunit --testsuite Integration
```

## Structure du projet

```
Reservation_mouhamed/
├── config/
│   ├── container.php          # conteneur PHP-DI
│   └── database.php           # démarrage Eloquent (Capsule\Manager)
├── database/
│   ├── migrations/            # schéma des tables
│   ├── migrer.php             # exécuteur de migrations
│   └── seed.php               # données initiales (5 salles)
├── docker/
│   ├── attend_db.php         # attente de la disponibilité de MySQL
│   └── entrypoint.sh         # migrations + seed + serveur PHP intégré
├── public/
│   ├── assets/style.css
│   └── index.php              # UNIQUE point d'entrée
├── routes/
│   └── web.php                # déclaration des routes FastRoute
├── src/
│   ├── Controller/            # SalleController, ReservationController
│   ├── DTO/                   # CreerSalleDTO, CreerReservationDTO
│   ├── Exception/
│   ├── Model/                 # Salle, Reservation (Eloquent)
│   ├── Repository/            # interfaces + implémentations Eloquent
│   ├── Service/               # règles métier
│   ├── Validation/            # validateurs + ValidationResult
│   ├── View/                  # moteur de rendu des templates
│   └── Application.php        # front controller / routeur
├── templates/
│   ├── error/
│   ├── layout/base.php
│   ├── reservation/
│   └── salle/
├── tests/
│   ├── Fakes/                 # repositories en mémoire
│   ├── Integration/
│   └── Unit/
├── ARCHITECTURE.md            # analyse des choix architecturaux
├── CHANGELOG.md
├── composer.json
├── composer.lock
├── docker-compose.yml
├── Dockerfile
├── .env.example
├── .gitignore
├── phpunit.xml
└── README.md
```

> Justifications détaillées des choix (MVC, Repository, Service, injection,
> SOLID…) : voir **[ARCHITECTURE.md](ARCHITECTURE.md)**.