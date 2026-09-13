<?php

declare(strict_types=1);

/**
 * Vérifie que la base accepte les connexions (utilisé par l'entrypoint).
 *
 * Usage : php attend_db.php PILOTE HOST PORT USER PASSWORD
 * Code de sortie : 0 = prêt, 1 = pas encore joignable.
 */

[$pilote, $hote, $port, $utilisateur, $motDePasse] = array_pad(array_slice($argv, 1), 5, null);

try {
    $dsn = $pilote === 'pgsql'
        ? "pgsql:host={$hote};port={$port}"
        : "mysql:host={$hote};port={$port};charset=utf8mb4";

    new PDO(
        $dsn,
        $utilisateur ?? 'root',
        $motDePasse ?? '',
        [
            PDO::ATTR_TIMEOUT => 2,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    );

    exit(0);
} catch (Throwable) {
    exit(1);
}
