<?php
/*
 * ============================================================================
 *  src/services/EscaneoService.php  ->  LÓGICA DE NEGOCIO DE LOS ESCANEOS
 * ============================================================================
 *  Capa: SERVICE (servicio). Está entre el controlador y el repositorio.
 *    - Coordina los pasos: escanear con Python + guardar en la base.
 *    - Maneja las TRANSACCIONES (todo o nada).
 *    - NO escribe SQL (eso es del repositorio) ni HTML (eso es de las vistas).
 *
 *  ¿Qué es una TRANSACCIÓN? (pregunta típica)
 *    Un grupo de operaciones en la base que se hacen TODAS o NINGUNA.
 *    Ejemplo: guardamos el escaneo y después 5 puertos. Si el 3er puerto
 *    falla, sin transacción quedaría un escaneo a medias. Con transacción se
 *    hace rollBack y la base vuelve a como estaba antes.
 *      beginTransaction() -> empieza
 *      commit()           -> confirma todo
 *      rollBack()         -> deshace todo
 * ============================================================================
 */
namespace App\services;

use App\persistence\EscaneoRepository;
use PDO;
use Throwable;

// Lógica de los escaneos. Las escrituras (crear, editar, borrar) van en una transacción.
// El service solo maneja la transacción; las consultas SQL están en el repositorio.
class EscaneoService
{
    // Recibe 3 dependencias por constructor (inyección de dependencias):
    //   $pdo     -> la conexión, para abrir/cerrar transacciones.
    //   $repo    -> el repositorio, para las consultas SQL.
    //   $scanner -> el servicio que ejecuta el script de Python.
    public function __construct(
        private PDO $pdo,
        private EscaneoRepository $repo,
        private ScannerService $scanner
    ) {
    }

    // Ejecuta $operacion dentro de una transacción:
    // si todo sale bien hace commit; si algo falla hace rollBack y relanza el error.
    //
    // callable = "algo que se puede llamar" (una función). Así escribimos la
    // lógica de transacción UNA sola vez y la reusamos en crear, editar y borrar.
    // mixed = puede devolver cualquier tipo (lo que devuelva $operacion).
    public function enTransaccion(callable $operacion): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $resultado = $operacion();   // ejecutamos lo que nos pasaron
            $this->pdo->commit();        // salió bien: confirmamos
            return $resultado;
        } catch (Throwable $error) {     // Throwable atrapa cualquier error/excepción
            $this->pdo->rollBack();      // salió mal: deshacemos todo
            throw $error;                // y avisamos hacia arriba (al controlador)
        }
    }

    // Lectura: no necesita transacción. Solo delega al repositorio.
    public function listar(?int $limite): array
    {
        return $this->repo->listar($limite);
    }

    // Devuelve el escaneo con sus puertos, o null si no existe.
    // ?array = devuelve un array o null.
    public function ver(int $id): ?array
    {
        $escaneo = $this->repo->buscar($id);
        if ($escaneo === null) {
            return null;
        }
        // Juntamos datos de dos tablas: el escaneo + su lista de puertos.
        $escaneo['puertos'] = $this->repo->puertosDe($id);
        return $escaneo;
    }

    // Escanea la IP y guarda el escaneo y sus puertos, todo junto o nada.
    public function crear(string $objetivo, ?string $notas): int
    {
        // 1) Escaneamos ANTES de abrir la transacción, porque tarda (hasta
        //    unos segundos). No conviene tener la base "bloqueada" esperando.
        $resultado = $this->scanner->escanear($objetivo); // antes de abrir la transacción (tarda)

        // 2) Guardamos todo dentro de UNA transacción.
        //    "use (...)" pasa variables de afuera a la función anónima.
        return $this->enTransaccion(function () use ($objetivo, $notas, $resultado) {
            // Guardamos el escaneo; el JSON completo del escáner queda en la
            // columna "resultados" como respaldo. Nos devuelve el id nuevo.
            $id = $this->repo->crear($objetivo, $notas, json_encode($resultado));
            // Una fila en la tabla "puertos" por cada puerto abierto encontrado.
            foreach ($resultado['abiertos'] as $p) {
                $this->repo->agregarPuerto($id, $p['puerto'], $p['servicio'], $p['riesgo']);
            }
            return $id; // enTransaccion devuelve este id al controlador
        });
    }

    // fn () => ... es una "arrow function": una función anónima corta de una línea.
    public function actualizarNotas(int $id, ?string $notas): void
    {
        $this->enTransaccion(fn () => $this->repo->actualizarNotas($id, $notas));
    }

    public function borrar(int $id): void
    {
        $this->enTransaccion(fn () => $this->repo->borrar($id));
    }
}
