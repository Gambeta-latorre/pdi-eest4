<?php
/*
 * ============================================================================
 *  src/persistence/EscaneoRepository.php  ->  REPOSITORIO (todo el SQL)
 * ============================================================================
 *  Capa: PERSISTENCIA. Patrón REPOSITORIO: una clase que es la ÚNICA que
 *  escribe SQL sobre las tablas "escaneos" y "puertos". El resto de la app
 *  le pide cosas con métodos (listar, buscar, crear...) sin saber SQL.
 *  Si mañana cambiamos de base de datos, solo se toca este archivo.
 *
 *  SEGURIDAD - SQL INJECTION (pregunta típica):
 *    NUNCA pegamos datos del usuario dentro del texto SQL. Usamos PREPARED
 *    STATEMENTS con "?" o ":nombre": la consulta se manda primero y los datos
 *    después, por separado. Así, aunque alguien escriba  ' OR 1=1 --  en las
 *    notas, se guarda como texto y no se ejecuta como SQL.
 *      prepare()  -> prepara la consulta con los huecos (?)
 *      execute()  -> la ejecuta llenando los huecos con los datos
 * ============================================================================
 */
namespace App\persistence;

use PDO;

// Única clase que habla con las tablas "escaneos" y "puertos".
class EscaneoRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    // LISTADO para el historial, con la CANTIDAD de puertos abiertos de cada uno.
    public function listar(?int $limite): array
    {
        // - "e" y "p" son alias cortos de las tablas.
        // - LEFT JOIN: trae TODOS los escaneos, aunque no tengan puertos
        //   (con un JOIN normal, los escaneos con 0 puertos desaparecerían).
        // - COUNT(p.id) cuenta los puertos; GROUP BY e.id agrupa por escaneo.
        // - ORDER BY fecha DESC: los más nuevos primero (id DESC desempata).
        $sql = 'SELECT e.id, e.objetivo, e.fecha, e.notas, COUNT(p.id) AS abiertos
                FROM escaneos e
                LEFT JOIN puertos p ON p.escaneo_id = e.id
                GROUP BY e.id
                ORDER BY e.fecha DESC, e.id DESC';
        // El LIMIT solo se agrega si pidieron ?limit=N.
        if ($limite !== null) {
            $sql .= ' LIMIT :limite'; // :limite = parámetro con nombre
        }

        $stmt = $this->pdo->prepare($sql);
        if ($limite !== null) {
            // bindValue con PDO::PARAM_INT: LIMIT necesita un número, no un texto.
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(); // todas las filas como array de arrays
    }

    // UN escaneo por id, o null si no existe.
    public function buscar(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM escaneos WHERE id = ?');
        $stmt->execute([$id]); // el ? se reemplaza por $id de forma segura
        // fetch() devuelve la fila o false si no hay. "?: null" convierte false en null.
        return $stmt->fetch() ?: null;
    }

    // Los puertos abiertos de un escaneo, ordenados de menor a mayor.
    public function puertosDe(int $escaneoId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT puerto, servicio, riesgo FROM puertos WHERE escaneo_id = ? ORDER BY puerto'
        );
        $stmt->execute([$escaneoId]);
        return $stmt->fetchAll();
    }

    // INSERTA un escaneo y devuelve su id nuevo.
    // NOW() = fecha y hora actual del servidor de base de datos.
    // $resultados = el JSON completo que devolvió Python (como respaldo).
    public function crear(string $objetivo, ?string $notas, string $resultados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO escaneos (objetivo, fecha, notas, resultados) VALUES (?, NOW(), ?, ?)'
        );
        $stmt->execute([$objetivo, $notas, $resultados]);
        // lastInsertId() = el id AUTO_INCREMENT que MySQL le acaba de dar.
        return (int) $this->pdo->lastInsertId();
    }

    // INSERTA un puerto abierto asociado a un escaneo (escaneo_id = clave foránea).
    public function agregarPuerto(int $escaneoId, int $puerto, string $servicio, string $riesgo): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO puertos (escaneo_id, puerto, servicio, riesgo) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$escaneoId, $puerto, $servicio, $riesgo]);
    }

    // ACTUALIZA solo las notas. El WHERE id = ? es clave: sin él se
    // modificarían TODOS los escaneos.
    public function actualizarNotas(int $id, ?string $notas): void
    {
        $stmt = $this->pdo->prepare('UPDATE escaneos SET notas = ? WHERE id = ?');
        $stmt->execute([$notas, $id]);
    }

    // BORRA un escaneo.
    // Los puertos se borran solos gracias al ON DELETE CASCADE.
    // (La clave foránea de "puertos" está definida en la migración 004.)
    public function borrar(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM escaneos WHERE id = ?');
        $stmt->execute([$id]);
    }
}
