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

    public function listar(int $usuarioId, ?int $limite): array
    {
        return $this->repo->listar($usuarioId, $limite);
    }

    // Devuelve el escaneo con sus puertos, o null si no existe o no es del usuario.
    public function ver(int $id, int $usuarioId): ?array
    {
        $escaneo = $this->repo->buscar($id, $usuarioId);
        if ($escaneo === null) {
            return null;
        }
        $escaneo['puertos'] = $this->repo->puertosDe($id);
        return $escaneo;
    }

    // Escanea la IP y guarda el escaneo y sus puertos, todo junto o nada.
    public function crear(int $usuarioId, string $objetivo, ?string $notas): int
    {
        $resultado = $this->scanner->escanear($objetivo); // antes de abrir la transacción (tarda)

        return $this->enTransaccion(function () use ($usuarioId, $objetivo, $notas, $resultado) {
            $id = $this->repo->crear($usuarioId, $objetivo, $notas, json_encode($resultado));
            foreach ($resultado['abiertos'] as $p) {
                $this->repo->agregarPuerto($id, $p['puerto'], $p['servicio'], $p['riesgo']);
            }
            return $id;
        });
    }

    public function actualizarNotas(int $id, int $usuarioId, ?string $notas): void
    {
        $this->enTransaccion(fn () => $this->repo->actualizarNotas($id, $usuarioId, $notas));
    }

    public function borrar(int $id, int $usuarioId): void
    {
        $this->enTransaccion(fn () => $this->repo->borrar($id, $usuarioId));
    }
}
