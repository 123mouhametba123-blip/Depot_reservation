<?php
/**
 * Gabarit général (layout).
 * Variables disponibles :
 *   - $titre   : titre de la page ;
 *   - $contenu : HTML déjà rendu de la vue courante.
 * Affiche le message de succès flash s'il existe (message de session).
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$succes = $_SESSION['succes'] ?? null;
unset($_SESSION['succes']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars((string) $titre, ENT_QUOTES, 'UTF-8') ?> — Réservation de salles</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="barre">
    <div class="conteneur">
        <a class="marque" href="/">🏫 Réservation de salles</a>
        <nav>
            <a href="/">Accueil</a>
            <a href="/salles">Salles</a>
            <a href="/salles/create">Ajouter une salle</a>
            <a href="/reservations">Réservations</a>
            <a href="/reservations/create">Réserver</a>
        </nav>
    </div>
</header>

<main class="conteneur">
    <?php if ($succes !== null): ?>
        <div class="bien-suivi succes"><?= htmlspecialchars((string) $succes, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?= $contenu ?>
</main>

<footer class="pied">
    <div class="conteneur">Projet ODC-P8 — Gestion des réservations de salles universitaires</div>
</footer>
</body>
</html>
