<?php

/**
 * Gabarit principal du site.
 *
 * @var string $contenu
 * @var string $titre
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$succes = $_SESSION['succes'] ?? null;
unset($_SESSION['succes']);

$pageActuelle = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';

$estActive = static function (string $racine) use ($pageActuelle): string {
    if ($racine === '/') {
        return $pageActuelle === '/' ? ' class="actif"' : '';
    }
    return str_starts_with($pageActuelle, $racine) ? ' class="actif"' : '';
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre) ?> — Réservation de salles</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="barre">
    <div class="conteneur">
        <a class="marque" href="/"><span class="logo">🏫</span> Réservation de salles</a>
        <nav>
            <a href="/"<?= $estActive('/') ?>>Accueil</a>
            <a href="/salles"<?= $estActive('/salles') ?>>Salles</a>
            <a href="/reservations"<?= $estActive('/reservations') ?>>Réservations</a>
            <a href="/reservations/create">+ Réserver</a>
        </nav>
    </div>
</header>
<main>
    <div class="conteneur" id="contenu">
        <?php if ($succes !== null) : ?>
            <div class="bien-suivi succes"><?= htmlspecialchars($succes) ?></div>
        <?php endif; ?>

        <?= $contenu ?>
    </div>
</main>
<footer class="pied">
    <div class="conteneur">Mon projet de gestion de salles universitaires — Projet ODC-P8</div>
</footer>
</body>
</html>