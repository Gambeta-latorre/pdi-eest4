<?php
/*
 * ============================================================================
 *  src/services/ScannerService.php  ->  PUENTE ENTRE PHP Y PYTHON
 * ============================================================================
 *  PHP no escanea puertos: ejecuta el script scanner/escanear.py como un
 *  proceso aparte, le pasa la IP, y lee lo que el script imprime (un JSON).
 *
 *     PHP  --(python escanear.py 192.168.0.1)-->  Python
 *     PHP  <--( {"ok": true, "abiertos": [...]} )--  Python
 *
 *  ¿Por qué Python? Porque tiene sockets e hilos muy simples en su librería
 *  estándar, y así repartimos responsabilidades: PHP = web, Python = escaneo.
 *  La comunicación es por JSON, un formato de texto que los dos entienden.
 * ============================================================================
 */
namespace App\services;

use RuntimeException;

// Ejecuta el script Python que escanea los puertos y devuelve su JSON como array.
class ScannerService
{
    // $script = ruta a escanear.py ; $python = ejecutable ("python" o "python3").
    public function __construct(private string $script, private string $python)
    {
    }

    // Misma regla que el script Python: solo IPv4 privadas o loopback.
    // "static" = se usa sin crear un objeto: ScannerService::ipPermitida($ip).
    // La validamos en PHP Y en Python (doble control / defensa en profundidad):
    // aunque alguien ejecute el script directo, igual no escanea IPs públicas.
    public static function ipPermitida(string $ip): bool
    {
        // 1) ¿Es una IPv4 válida? (ej: "999.1.1.1" o "hola" -> false)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }
        // 2) ¿Es loopback (127.x.x.x = la propia PC)?
        $esLoopback = str_starts_with($ip, '127.');
        // 3) ¿Es privada (10.x, 172.16-31.x, 192.168.x)?
        //    Truco: le pedimos a PHP que valide la IP PROHIBIENDO rangos privados.
        // Si al prohibir rangos privados la IP deja de ser válida, es porque es privada.
        $esPrivada = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false;
        return $esLoopback || $esPrivada;
    }

    public function escanear(string $ip): array
    {
        // SEGURIDAD - inyección de comandos (pregunta típica):
        // Si armáramos un texto como "python escanear.py $ip" y lo pasáramos a
        // la consola, alguien podría mandar "1.1.1.1; borrar todo".
        // Los argumentos van como array: proc_open NO usa la shell,
        // así que no hay forma de inyectar comandos a través de la IP.
        // (Y además la IP ya fue validada antes.)
        $comando = [$this->python, $this->script, $ip];

        // "Tuberías" (pipes) para leer lo que imprime el proceso:
        //   1 = stdout (salida normal -> el JSON)
        //   2 = stderr (salida de errores)
        $tuberias = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];

        // Arranca el proceso de Python. $pipes queda con los canales abiertos.
        $proceso = proc_open($comando, $tuberias, $pipes);
        if (!is_resource($proceso)) {
            // Por ejemplo: Python no está instalado.
            throw new RuntimeException('No se pudo ejecutar el escáner');
        }

        // Leemos todo lo que imprimió (espera a que termine).
        $salida = stream_get_contents($pipes[1]);
        $errores = stream_get_contents($pipes[2]);
        // Cerramos los canales y el proceso (liberar recursos).
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proceso);

        // Convertimos el texto JSON en array de PHP (true = array asociativo).
        $datos = json_decode($salida, true);
        if (!is_array($datos)) {
            // No vino un JSON: algo falló en Python. Mostramos el detalle.
            throw new RuntimeException('El escáner no devolvió un JSON válido. ' . trim($errores));
        }
        if (empty($datos['ok'])) {
            // Vino JSON pero con ok=false (ej: IP no permitida).
            throw new RuntimeException($datos['error'] ?? 'Error desconocido del escáner');
        }
        // Todo bien: devolvemos el array con "abiertos", "fecha", etc.
        return $datos;
    }
}
