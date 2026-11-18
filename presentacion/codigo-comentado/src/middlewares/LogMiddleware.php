<?php
/*
 * ============================================================================
 *  src/middlewares/LogMiddleware.php  ->  MIDDLEWARE DE LOG (registro)
 * ============================================================================
 *  ¿Qué es un middleware?
 *    Código que se ejecuta ANTES y DESPUÉS de cada petición, sin importar la
 *    ruta. Es ideal para cosas "transversales" como registrar (loguear).
 *
 *  ¿Qué hace este?
 *    Por cada petición anota una línea así en la consola y en logs/app.log:
 *      [2026-11-18 10:15:02] POST /escaneos 201 1534.2ms
 *       fecha y hora          método ruta  status  tiempo que tardó
 *
 *  Se registra como GLOBAL en bootstrap.php (aplica a todas las rutas).
 * ============================================================================
 */
namespace App\middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

// "implements MiddlewareInterface" = promete tener el método process().
// Es el estándar PSR-15, por eso Slim sabe cómo usarlo.
class LogMiddleware implements MiddlewareInterface
{
    // Ruta del archivo de log (se la pasamos desde bootstrap.php).
    public function __construct(private string $archivo)
    {
    }

    public function process(Request $request, Handler $handler): Response
    {
        // ANTES: guardamos el momento de inicio (en segundos, con decimales).
        $inicio = microtime(true);

        // $handler->handle() pasa la petición al resto de la app (otros
        // middlewares, la ruta y el controlador) y nos devuelve la respuesta.
        $response = $handler->handle($request);

        // DESPUÉS: calculamos cuánto tardó y lo pasamos a milisegundos.
        $ms = (microtime(true) - $inicio) * 1000;

        // sprintf arma el texto con formato:
        //   %s = texto, %d = entero, %.1f = número con 1 decimal.
        $linea = sprintf(
            '[%s] %s %s %d %.1fms',
            date('Y-m-d H:i:s'),           // fecha y hora
            $request->getMethod(),          // GET, POST, PUT, DELETE
            $request->getUri()->getPath(),  // ej: /escaneos/5
            $response->getStatusCode(),     // ej: 200, 404
            $ms                             // tiempo
        );

        error_log($linea);                                                // consola
        // FILE_APPEND = agregar al final del archivo, sin borrar lo anterior.
        // PHP_EOL = salto de línea correcto para el sistema operativo.
        file_put_contents($this->archivo, $linea . PHP_EOL, FILE_APPEND); // logs/app.log

        // Devolvemos la respuesta SIN modificarla: solo la observamos.
        return $response;
    }
}
