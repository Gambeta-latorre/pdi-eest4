<?php
/*
 * ============================================================================
 *  scripts/migrate.php  ->  EJECUTOR DE MIGRACIONES
 * ============================================================================
 *  ¿Qué es una MIGRACIÓN? (pregunta típica)
 *    Un archivo .sql numerado con UN cambio en la estructura de la base
 *    (crear una tabla, agregar una columna, una clave foránea...).
 *    Es como un "historial de versiones" de la base de datos:
 *      - Cualquier compañero corre "composer migrate" y tiene la misma base.
 *      - Nunca se edita una migración vieja: si hay que cambiar algo, se
 *        agrega una nueva (por eso existe la 007 en vez de editar la 002).
 *
 *  Este script:
 *    1) Crea la base de datos si no existe.
 *    2) Crea la tabla "migraciones" que anota cuáles ya se ejecutaron.
 *    3) Ejecuta en orden SOLO las que faltan.
 *  Se puede correr muchas veces sin romper nada (es "idempotente").
 *
 *  Uso: composer migrate   (definido en composer.json -> "scripts")
 * ============================================================================
 */
// Ejecuta las migraciones SQL en orden (001, 002, ...) y anota cuáles ya corrieron.
// Uso: composer migrate

use App\persistence\Database;
use Dotenv\Dotenv;

// Mismo arranque que la app: autoload y variables del .env.
require __DIR__ . '/../vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$base = $_ENV['DB_NAME'] ?? 'trafficop';

// 1) Crear la base si no existe (el nombre viene del .env, solo letras, números y _)
// El nombre de una base NO se puede pasar como "?" en un prepared statement,
// así que lo validamos con una expresión regular: \w = letra, número o _.
if (!preg_match('/^\w+$/', $base)) {
    exit("Nombre de base inválido en DB_NAME\n");
}
// conexion(false) = conectarse al servidor sin elegir base (todavía no existe).
Database::conexion(false)->exec("CREATE DATABASE IF NOT EXISTS `$base` CHARACTER SET utf8mb4");
// Ahora sí, nos conectamos a la base.
$pdo = Database::conexion();

// 2) Tabla que registra las migraciones ejecutadas
// archivo es PRIMARY KEY: no se puede anotar dos veces la misma migración.
$pdo->exec('CREATE TABLE IF NOT EXISTS migraciones (
    archivo VARCHAR(255) PRIMARY KEY,
    ejecutada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)');
// FETCH_COLUMN = traer solo la primera columna como lista simple:
// ['001_crear_usuarios.sql', '002_crear_escaneos.sql', ...]
$hechas = $pdo->query('SELECT archivo FROM migraciones')->fetchAll(PDO::FETCH_COLUMN);

// 3) Recorrer los .sql en orden alfabético (por eso van numerados)
// glob busca todos los archivos que terminan en .sql en esa carpeta.
$archivos = glob(__DIR__ . '/../src/database/migrations/*.sql');
sort($archivos); // 001 antes que 002, etc. El orden importa: la FK (003) necesita las tablas.

$nuevas = 0;
foreach ($archivos as $ruta) {
    $nombre = basename($ruta); // solo el nombre del archivo, sin la carpeta
    if (in_array($nombre, $hechas, true)) {
        // Ya se corrió antes: la salteamos.
        echo "  ya ejecutada: $nombre\n";
        continue;
    }

    // Ejecutamos el SQL del archivo y la anotamos como hecha.
    $pdo->exec(file_get_contents($ruta));
    $stmt = $pdo->prepare('INSERT INTO migraciones (archivo) VALUES (?)');
    $stmt->execute([$nombre]);
    echo "  OK ejecutada: $nombre\n";
    $nuevas++;
}

// Operador ternario: condición ? si es verdad : si es falso.
echo $nuevas === 0 ? "No hay migraciones nuevas.\n" : "Listo: $nuevas migración(es) nueva(s).\n";
