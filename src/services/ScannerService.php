<?php
namespace App\services;

use RuntimeException;

// Ejecuta el script Python que escanea los puertos y devuelve su JSON como array.
class ScannerService
{
    public function __construct(private string $script, private string $python)
    {
    }

    // Misma regla que el script Python: solo IPv4 privadas o loopback.
    public static function ipPermitida(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }
        $esLoopback = str_starts_with($ip, '127.');
        // Si al prohibir rangos privados la IP deja de ser válida, es porque es privada.
        $esPrivada = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false;
        return $esLoopback || $esPrivada;
    }

    public function escanear(string $ip): array
    {
        // Los argumentos van como array: proc_open NO usa la shell,
        // así que no hay forma de inyectar comandos a través de la IP.
        $comando = [$this->python, $this->script, $ip];
        $tuberias = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];

        $proceso = proc_open($comando, $tuberias, $pipes);
        if (!is_resource($proceso)) {
            throw new RuntimeException('No se pudo ejecutar el escáner');
        }

        $salida = stream_get_contents($pipes[1]);
        $errores = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($proceso);

        $datos = json_decode($salida, true);
        if (!is_array($datos)) {
            throw new RuntimeException('El escáner no devolvió un JSON válido. ' . trim($errores));
        }
        if (empty($datos['ok'])) {
            throw new RuntimeException($datos['error'] ?? 'Error desconocido del escáner');
        }
        return $datos;
    }
}
