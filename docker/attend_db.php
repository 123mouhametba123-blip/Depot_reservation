<?php

declare(strict_types=1);

/**
 * Vérifie que MySQL accepte les connexions (utilisé par l'entrypoint).
 *
 * Usage : php attend_db.php HOST PORT USER PASSWORD
 * Code de sortie : 0 = prêt, 1 = pas encore joignable.
 */

[$hote, $port, $utilisateur, $motDePasse] = array_pad(array_slice($argv, 1), 4, null);

$options = [
    PDO::ATTR_TIMEOUT => 2,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

// Même logique TLS que config/database.php (TiDB impose SSL).
$ssl = getenv('DB_SSL');
if (in_array($ssl, ['1', 'true', 'yes', 'on'], true)) {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    $options[PDO::MYSQL_ATTR_SSL_CA] = getenv('DB_SSL_CA') ?: '/etc/ssl/certs/ca-certificates.crt';
}

try {
    new PDO(
        "mysql:host={$hote};port={$port};charset=utf8mb4",
        $utilisateur ?? 'root',
        $motDePasse ?? '',
        $options,
    );

    exit(0);
} catch (Throwable) {
    exit(1);
}