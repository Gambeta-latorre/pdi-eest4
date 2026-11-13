<?php
namespace App\persistence;

use PDO;

// Única clase que habla con las tablas "escaneos" y "puertos".
class EscaneoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(?int $limite): array
    {
        $sql = 'SELECT e.id, e.objetivo, e.fecha, e.notas, COUNT(p.id) AS abiertos
                FROM escaneos e
                LEFT JOIN puertos p ON p.escaneo_id = e.id
                GROUP BY e.id
                ORDER BY e.fecha DESC, e.id DESC';
        if ($limite !== null) {
            $sql .= ' LIMIT :limite';
        }

        $stmt = $this->pdo->prepare($sql);
        if ($limite !== null) {
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM escaneos WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function puertosDe(int $escaneoId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT puerto, servicio, riesgo FROM puertos WHERE escaneo_id = ? ORDER BY puerto'
        );
        $stmt->execute([$escaneoId]);
        return $stmt->fetchAll();
    }

    public function crear(string $objetivo, ?string $notas, string $resultados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO escaneos (objetivo, fecha, notas, resultados) VALUES (?, NOW(), ?, ?)'
        );
        $stmt->execute([$objetivo, $notas, $resultados]);
        return (int) $this->pdo->lastInsertId();
    }

    public function agregarPuerto(int $escaneoId, int $puerto, string $servicio, string $riesgo): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO puertos (escaneo_id, puerto, servicio, riesgo) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$escaneoId, $puerto, $servicio, $riesgo]);
    }

    public function actualizarNotas(int $id, ?string $notas): void
    {
        $stmt = $this->pdo->prepare('UPDATE escaneos SET notas = ? WHERE id = ?');
        $stmt->execute([$notas, $id]);
    }

    // Los puertos se borran solos gracias al ON DELETE CASCADE.
    public function borrar(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM escaneos WHERE id = ?');
        $stmt->execute([$id]);
    }
}
