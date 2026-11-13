<?php
namespace App\persistence;

use PDO;

// Crea la conexión PDO a MySQL/MariaDB con los datos del .env.
class Database
{
    public static function conexion(bool $conBase = true): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4%s',
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            $_ENV['DB_PORT'] ?? '3306',
            $conBase ? ';dbname=' . ($_ENV['DB_NAME'] ?? 'trafficop') : ''
        );

        return new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // filas como arrays asociativos
            PDO::ATTR_EMULATE_PREPARES => false,               // prepared statements reales
        ]);
    }
}
