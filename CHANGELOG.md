# Changelog

Toutes les modifications notables de ce projet sont documentées dans ce
fichier. Le format s'inspire de [Keep a Changelog](https://keepachangelog.com/fr/).

Le numéro de version suit le [SemVer](https://semver.org/lang/fr/) et chaque
étape de l'exercice correspond à une branche `feature/XX` taguée.

## [v1.0.0] — Release 1.0.0 (branche `release/1.0.0`)

### Ajouté
- Mise en forme CSS (`public/assets/style.css`).
- Messages de succès et d'erreur par champ + conservation des valeurs valides.
- Gestion propre des erreurs (404, 405 avec en-tête `Allow`, erreurs métier).
- Documentation complète : README, CHANGELOG, ARCHITECTURE, diagramme de classes.
- Docker : `Dockerfile` (Nginx + PHP-FPM), `docker-compose.yml`,
  entrypoint (attente MySQL, migrations, seed).

## [v0.12.0] — Tests (branche `feature/12-tests`)

### Ajouté
- `tests/Unit/CreerReservationServiceTest` : 8 situations du service de création.
- `tests/Unit/AnnulerReservationServiceTest` : annulation.
- `tests/Unit/ValidationTest` : 5 cas de validation obligatoires.
- `tests/Integration/EloquentReservationTest` : Eloquent sur SQLite en mémoire
  ou MySQL dédié (bascule automatique).
- `tests/Fakes/` : implémentations en mémoire des repositories (aucun MySQL requis).
- `phpunit.xml` + bootstrap de test.

## [v0.11.0] — Conteneur d'injection (branche `feature/11-container`)

### Ajouté
- `config/container.php` : PHP-DI avec autowiring, définitions d'interfaces et factories.
- `public/index.php` simplifié : démarre Eloquent (Capsule global), puis `$container->get(Application::class)->run()`.
- Résolution des contrôleurs par le conteneur dans `Application`.

## [v0.10.0] — Routeur (branche `feature/10-router`)

### Ajouté
- Routes de l'énoncé dans `routes/web.php` (handlers `[Contrôleur::class, 'action']`).
- Dispatcher FastRoute et traitement de `FOUND` / `NOT_FOUND` / `METHOD_NOT_ALLOWED`.
- Suppression de la query string, en-tête `Allow` pour les 405.

## [v0.9.0] — Contrôleurs et vues (branche `feature/09-interface-web`)

### Ajouté
- `SalleController` : `index`, `show`, `create`, `store`, `edit`, `update`.
- `ReservationController` : `index`, `show`, `create`, `store`, `cancel`.
- Vues `salle/`, `reservation/`, `error/`, gabarit `layout/base.php`.
- Échappement de toutes les sorties dynamiques, redirections après POST réussi.

## [v0.8.0] — Services (branche `feature/08-services`)

### Ajouté
- `CreerReservationService` : les 9 règles d'acceptation d'une réservation.
- `AnnulerReservationService`.
- `SalleIndisponibleException`, `ReservationIntrouvableException`.

## [v0.7.0] — Repositories (branche `feature/07-repositories`)

### Ajouté
- `SalleRepositoryInterface`, `ReservationRepositoryInterface`.
- Implémentations Eloquent (lister, retrouver, enregistrer, chevauchement, annuler).
- Les contrôleurs ne contiennent plus aucune requête ORM.

## [v0.6.0] — DTO (branche `feature/06-dto`)

### Ajouté
- `CreerSalleDTO`, `CreerReservationDTO` avec données typées
  (`salleId:int`, `dateDebut/dateFin:DateTimeImmutable`, …).

## [v0.5.0] — Validation (branche `feature/05-validation`)

### Ajouté
- `ValidatorInterface`, `ValidationResult` (valide, erreurs par champ, données acceptées).
- `SalleValidator`, `ReservationValidator` basés sur `respect/validation`.

## [v0.4.0] — Données initiales (branche `feature/04-donnees-initiales`)

### Ajouté
- `database/seed.php` : 5 salles, idempotent (aucun doublon en cas de relance).

## [v0.3.0] — Modèles (branche `feature/03-modeles`)

### Ajouté
- `Salle` (hasMany → `reservations`), `Reservation` (belongsTo → `salle`).
- Tables, `$fillable`, conversions de types, dates.

## [v0.2.0] — Eloquent (branche `feature/02-eloquent`)

### Ajouté
- `.env.example`, chargement via `vlucas/phpdotenv`, `Capsule\Manager`.
- Schéma des tables `salles` et `reservations` (`database/migrations/`).
- Migrations « rejouables » : clés étrangères désactivées le temps de la réinitialisation.

## [v0.1.0] — Projet Composer (branche `feature/01-composer`)

### Ajouté
- `composer.json`, autoloading PSR-4 (`App\` → `src/`).
- Dépendances : fast-route, respect/validation, illuminate/database, php-di, phpdotenv.
- Arborescence du projet.

## [v0.0.0] — Initialisation du dépôt (branche `main`)

### Ajouté
- Initialisation Git, branche `main`.
- `.gitignore`, `README.md`, `CHANGELOG.md`.