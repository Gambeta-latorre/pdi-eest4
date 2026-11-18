<?php
/*
 * ============================================================================
 *  src/persistence/Database.php  ->  CONEXIÓN A LA BASE DE DATOS
 * ============================================================================
 *  Capa: PERSISTENCIA. Crea el objeto PDO que conecta PHP con MySQL/MariaDB.
 *  PDO (PHP Data Objects) es la forma estándar de PHP para hablar con bases
 *  de datos, y permite usar PREPARED STATEMENTS (protección contra SQL Injection).
 * ============================================================================
 */
namespace App\persistence;

use PDO;

// Crea la conexión PDO a MySQL/MariaDB con los datos del .env.
class Database
{
    // static: se llama como Database::conexion(), sin crear un objeto.
    // $conBase = true  -> se conecta a la base "trafficop" (uso normal).
    // $conBase = false -> se conecta al servidor SIN elegir base; lo usa
    //                     scripts/migrate.php para poder CREAR la base si no existe.
    public static function conexion(bool $conBase = true): PDO
    {
        // DSN = "Data Source Name": el texto que dice a qué servidor conectarse.
        // Ej: mysql:host=127.0.0.1;port=3306;charset=utf8mb4;dbname=trafficop
        // Los datos salen del .env; si faltan se usan los valores por defecto (??).
        // utf8mb4 = UTF-8 completo (tildes, ñ y hasta emojis como 🚦).
        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=utf8mb4%s',
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            $_ENV['DB_PORT'] ?? '3306',
            $conBase ? ';dbname=' . ($_ENV['DB_NAME'] ?? 'trafficop') : ''
        );

        // new PDO(dsn, usuario, contraseña, opciones)
        return new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
            // Si una consulta falla, PDO lanza una excepción (no falla en silencio).
            // Esto es lo que permite que el rollBack de las transacciones funcione.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // errores como excepciones
            // Cada fila viene como ['id' => 1, 'objetivo' => '127.0.0.1', ...].
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // filas como arrays asociativos
            // Que MySQL haga los prepared statements de verdad (no que PHP los
            // "simule"): más seguro y respeta los tipos (ej: LIMIT como entero).
            PDO::ATTR_EMULATE_PREPARES => false,               // prepared statements reales
        ]);
    }
}
