# Analyse des choix architecturaux

Ce document identifie et justifie chaque notion technique mise en œuvre dans
le projet. Pour chacune : **classes concernées**, **rôle**, **avantage**,
**limite ou risque** et **extrait représentatif**.

Le diagramme de classes est disponible dans [`docs/class-diagram.md`](docs/class-diagram.md).

---

## 1. MVC (Modèle‑Vue‑Contrôleur)

- **Classes** : `App\Model\*` (Modèle), `App\View\View` + `templates/*`
  (Vue), `App\Controller\*` (Contrôleur).
- **Rôle** : séparer les données (modèles), la présentation (vues) et
  l'orchestration des requêtes (contrôleurs).
- **Avantage** : chaque couche évolue indépendamment ; une vue ou un
  contrôleur se modifie sans toucher au modèle.
- **Limite** : sans discipline, le contrôleur accumule la logique métier.
  C'est ici le rôle des *services* de l'empêcher.
- **Extrait** :

```php
final class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly View $vue,
    ) {}

    public function index(): string
    {
        return $this->vue->rendu('salle/index.php', [
            'titre'  => 'Liste des salles',
            'salles' => $this->salles->lister(),
        ]);
    }
}
```

## 2. Front Controller

- **Classes** : `public/index.php`, `App\Application`.
- **Rôle** : canaliser toutes les requêtes HTTP vers un unique point
  d'entrée qui route, puis délègue.
- **Avantage** : point unique pour la sécurité transversale, la résolution
  du conteneur et la gestion centralisée des erreurs.
- **Limite** : chaque requête traverse une même chaîne (surcharge légère,
  négligeable ici).
- **Extrait** :

```php
$container   = $builder->build();
$application = $container->get(Application::class);
$application->run();
```

## 3. Router

- **Classes** : `routes/web.php`, `FastRoute\Dispatcher`, `App\Application`.
- **Rôle** : associer méthode + chemin HTTP à un gestionnaire
  (`[Contrôleur::class, 'action']`), extraire les paramètres dynamiques,
  produire 404/405.
- **Avantage** : aucune logique de routage dans les contrôleurs, déclaration
  centralisée, paramètres contraints (`{id:\d+}`).
- **Limite** : FastRoute ne construit pas les contrôleurs — c'est au front
  controller d'utiliser le conteneur.
- **Extrait** :

```php
$r->addRoute('POST', '/reservations/{id:\d+}/cancel', [ReservationController::class, 'cancel']);
```

## 4. Validator

- **Classes** : `ValidatorInterface`, `ValidationResult`,
  `SalleValidator`, `ReservationValidator` (via `respect/validation`).
- **Rôle** : contrôler les données HTTP *avant* leur usage, sans persister
  quoi que ce soit.
- **Avantage** : validation syntaxique séparée des règles métier, résultats
  immuables, plusieurs erreurs par champ.
- **Limite** : la comparaison entre dates relève de la couche métier
  (service), pas du validateur.
- **Extrait** :

```php
$resultat = $validateur->validate($donnees);
if (!$resultat->isValid()) {
    $_SESSION['erreurs']   = $resultat->errors();
    header('Location: /salles/create');
}
```

## 5. DTO (Data Transfer Object)

- **Classes** : `CreerSalleDTO`, `CreerReservationDTO`.
- **Rôle** : transporter des données typées entre les couches ; le
  tableau `$_POST` n'est jamais transmis directement aux services.
- **Avantage** : contrat de types (`int`, `DateTimeImmutable`, …), données
  immuables (`readonly`), testable.
- **Limite** : le DTO ne porte aucune règle (chevauchement, durée) — c'est
  volontaire, ces règles appartiennent aux services.
- **Extrait** :

```php
$dto = new CreerReservationDTO(
    salleId: $acceptees['salle_id'],
    responsable: $acceptees['responsable'],
    dateDebut: $acceptees['date_debut'],     // DateTimeImmutable
    dateFin: $acceptees['date_fin'],
);
```

## 6. ORM (Object‑Relational Mapping)

- **Classes** : `config/database.php`, `App\Model\*`, `illuminate/database`.
- **Rôle** : exposer les tables sous forme d'objets et traduire les accès en
  requêtes SQL.
- **Avantage** : pas de SQL écrit à la main pour les cas courants, gestion
  des conversions de types et des dates.
- **Limite** : masque la réalité des requêtes ; les accès complexes doivent
  être isolés (d'où le repository).
- **Extrait** :

```php
$capsule = new Capsule();
$capsule->addConnection($configuration);
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

## 7. Active Record

- **Classes** : `App\Model\Salle`, `App\Model\Reservation`.
- **Rôle** : un modèle porte à la fois données et comportement d'accès
  (`$salle->reservations`, `$reservation->salle`).
- **Avantage** : syntaxe lisible et rapide pour les relations.
- **Limite** : mélange persistance/logique ; les contrôleurs ne doivent pas
  l'exploiter directement (cf. repository).
- **Extrait** :

```php
return $this->hasMany(Reservation::class, 'salle_id');
```

## 8. Repository

- **Classes** : `SalleRepositoryInterface`, `ReservationRepositoryInterface`,
  `EloquentSalleRepository`, `EloquentReservationRepository`.
- **Rôle** : isoler **toutes** les requêtes ORM derrière une interface.
- **Avantage** : les contrôleurs/services ne contiennent jamais
  `Salle::query()`, `Reservation::where()`, `->save()` ; l'implémentation peut
  être remplacée (fake en mémoire pour les tests).
- **Limite** : abstraction intermédiaire utile ici par exigence pédagogique ;
  elle n'est pas toujours nécessaire dans les très petits projets.
- **Extrait** :

```php
public function trouverChevauchement(int $salleId, DateTimeImmutable $debut, DateTimeImmutable $fin): ?Reservation
{
    return Reservation::query()
        ->where('salle_id', $salleId)
        ->where('statut', Reservation::STATUT_CONFIRMEE)
        ->where('date_fin', '>', $debut->format('Y-m-d H:i:s'))
        ->where('date_debut', '<', $fin->format('Y-m-d H:i:s'))
        ->first();
}
```

## 9. Service

- **Classes** : `CreerReservationService`, `AnnulerReservationService`,
  `SalleIndisponibleException`, `ReservationIntrouvableException`.
- **Rôle** : porter les règles métier (les 9 conditions d'acceptation) sans
  connaître `$_POST`, FastRoute, les vues ni le conteneur.
- **Avantage** : règles testables sans HTTP ni base (doublures en mémoire).
- **Limite** : la lecture des entrées et l'affichage restent du ressort du
  contrôleur — le service n'en dépend pas.
- **Extrait** :

```php
if ($dto->dateDebut >= $dto->dateFin) {
    throw new SalleIndisponibleException('La date de début doit précéder la date de fin.');
}
```

## 10. Injection par constructeur

- **Classes** : `SalleController`, `ReservationController`,
  `CreerReservationService`, `AnnulerReservationService`, `App\Application`.
- **Rôle** : les dépendances sont déclarées comme paramètres du constructeur
  et fournies au moment de l'instanciation.
- **Avantage** : dépendances explicites, remplaçables (mocks), code plus
  testable et lisible.
- **Limite** : cérémonie d'instanciation → gérée par le conteneur.
- **Extrait** :

```php
final class CreerReservationService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations,
    ) {}
}
```

## 11. Conteneur d'injection

- **Classes** : `config/container.php`, `DI\ContainerBuilder`.
- **Rôle** : construire et assemble les objets de toute l'application via
  autowiring, définitions et factories.
- **Avantage** : composition centralisée, résolution à la demande au point
  d'entrée.
- **Limite** : ne doit pas devenir un « service locator » pour les classes
  métier (anti-pattern interdit par l'énoncé).
- **Extrait** :

```php
SalleRepositoryInterface::class =>
    autowire(EloquentSalleRepository::class),

Capsule::class =>
    factory(require dirname(__DIR__) . '/config/database.php'),
```

## 12. Autowiring

- **Classes** : toutes les classes concrètes simples (`SalleValidator`,
  `SalleController`, `CreerReservationService`, …).
- **Rôle** : PHP-DI déduit les dépendances à partir des types des
  constructeurs.
- **Avantage** : moins de configuration pour les classes du projet.
- **Limite** : les interfaces et les objets configurés (Capsule, Dispatcher)
  exigent une définition explicite.
- **Extrait** :

```php
ReservationController::class => autowire(ReservationController::class),
```

## 13. Inversion de contrôle (IoC)

- **Classes** : `conf.` conteneur ; services et contrôleurs.
- **Rôle** : ce n'est plus la classe qui choisit comment obtenir ses
  dépendances, c'est le conteneur qui les injecte.
- **Avantage** : découplage fort, conformité au principe D de SOLID.
- **Limite** : la configuration du conteneur devient le « vrai » point de
  câblage du projet.
- **Extrait** : la classe `CreerReservationService` *reçoit* ses repositories,
  elle n'appelle jamais `$container->get()`.

## 14. Les cinq principes SOLID

| Principe | Application dans le projet |
|---|---|
| **S** — Single Responsibility | chaque classe a une mission : le contrôleur orchestre, le service applique les règles, le repository accède aux données, le validateur contrôle. |
| **O** — Open/Closed | les fonctionnalités s'étendent par de nouvelles implémentations (ex. un autre repository) sans modifier les contrôleurs ni les services. |
| **L** — Liskov | les contrôleurs/services travaillent sur les interfaces `SalleRepositoryInterface` / `ReservationRepositoryInterface` : les fakes et les implémentations Eloquent sont interchangeables. |
| **I** — Interface Segregation | les interfaces Repository sont réduites aux opérations réellement nécessaires (`lister`, `trouverParId`, `enregistrer`, …). |
| **D** — Dependency Inversion | les classes de haut niveau (services) dépendent d'abstractions (interfaces), jamais de l'implémentation Eloquent concrète. |

### Extrait représentatif — S (séparation des responsabilités)

```php
// Contrôleur (orchestration)          Service (règle métier)     Repository (accès données)
$dto = new CreerReservationDTO(...);   // 9 vérifications          ->trouverChevauchement()
$r = $this->creerReservation->executer($dto);                      ->enregistrer($dto)
```

---

## Bilan

L'architecture suit un flux unique :

```
HTTP → public/index.php (Front Controller)
     → App\Application (Router FastRoute + conteneur PHP-DI)
     → Contrôleur (lit HTTP, valide, construit le DTO)
     → Service (règles métier)
     → Repository (interface) → implémentation Eloquent
     → Modèle → base MySQL
     → Vue (templates, sorties échappées) → HTML
```

Ce découpage respecte l'énoncé : point d'entrée unique, aucun `$_POST` brut
dans les services, aucune requête ORM dans les contrôleurs, aucune règle
métier dans les vues, et un seul accès direct au conteneur (le point
d'entrée).