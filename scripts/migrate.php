<?php
// Ejecuta las migraciones SQL en orden (001, 002, ...) y anota cuáles ya corrieron.
// Uso: composer migrate

use App\persistence\Database;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$base = $_ENV['DB_NAME'] ?? 'trafficop';

// 1) Crear la base si no existe (el nombre viene del .env, solo letras, números y _)
if (!preg_match('/^\w+$/', $base)) {
    exit("Nombre de base inválido en DB_NAME\n");
}
Database::conexion(false)->exec("CREATE DATABASE IF NOT EXISTS `$base` CHARACTER SET utf8mb4");
$pdo = Database::conexion();

// 2) Tabla que registra las migraciones ejecutadas
$pdo->exec('CREATE TABLE IF NOT EXISTS migraciones (
    archivo VARCHAR(255) PRIMARY KEY,
    ejecutada_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)');
$hechas = $pdo->query('SELECT archivo FROM migraciones')->fetchAll(PDO::FETCH_COLUMN);

// 3) Recorrer los .sql en orden alfabético (por eso van numerados)
$archivos = glob(__DIR__ . '/../src/database/migrations/*.sql');
sort($archivos);

$nuevas = 0;
foreach ($archivos as $ruta) {
    $nombre = basename($ruta);
    if (in_array($nombre, $hechas, true)) {
        echo "  ya ejecutada: $nombre\n";
        continue;
    }

    $pdo->exec(file_get_contents($ruta));
    $stmt = $pdo->prepare('INSERT INTO migraciones (archivo) VALUES (?)');
    $stmt->execute([$nombre]);
    echo "  OK ejecutada: $nombre\n";
    $nuevas++;
}

echo $nuevas === 0 ? "No hay migraciones nuevas.\n" : "Listo: $nuevas migración(es) nueva(s).\n";
