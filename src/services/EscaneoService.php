<?php
namespace App\services;

use App\persistence\EscaneoRepository;
use PDO;
use Throwable;

// Lógica de los escaneos. Las escrituras (crear, editar, borrar) van en una transacción.
// El service solo maneja la transacción; las consultas SQL están en el repositorio.
class EscaneoService
{
    public function __construct(
        private PDO $pdo,
        private EscaneoRepository $repo,
        private ScannerService $scanner
    ) {
    }

    // Ejecuta $operacion dentro de una transacción:
    // si todo sale bien hace commit; si algo falla hace rollBack y relanza el error.
    public function enTransaccion(callable $operacion): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $resultado = $operacion();
            $this->pdo->commit();
            return $resultado;
        } catch (Throwable $error) {
            $this->pdo->rollBack();
            throw $error;
        }
    }

    public function listar(?int $limite): array
    {
        return $this->repo->listar($limite);
    }

    // Devuelve el escaneo con sus puertos, o null si no existe.
    public function ver(int $id): ?array
    {
        $escaneo = $this->repo->buscar($id);
        if ($escaneo === null) {
            return null;
        }
        $escaneo['puertos'] = $this->repo->puertosDe($id);
        return $escaneo;
    }

    // Escanea la IP y guarda el escaneo y sus puertos, todo junto o nada.
    public function crear(string $objetivo, ?string $notas): int
    {
        $resultado = $this->scanner->escanear($objetivo); // antes de abrir la transacción (tarda)

        return $this->enTransaccion(function () use ($objetivo, $notas, $resultado) {
            $id = $this->repo->crear($objetivo, $notas, json_encode($resultado));
            foreach ($resultado['abiertos'] as $p) {
                $this->repo->agregarPuerto($id, $p['puerto'], $p['servicio'], $p['riesgo']);
            }
            return $id;
        });
    }

    public function actualizarNotas(int $id, ?string $notas): void
    {
        $this->enTransaccion(fn () => $this->repo->actualizarNotas($id, $notas));
    }

    public function borrar(int $id): void
    {
        $this->enTransaccion(fn () => $this->repo->borrar($id));
    }
}
