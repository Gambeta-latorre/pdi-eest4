<?php
namespace App\middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

// Registra cada petición: método, ruta, status y tiempo en milisegundos.
class LogMiddleware implements MiddlewareInterface
{
    public function __construct(private string $archivo)
    {
    }

    public function process(Request $request, Handler $handler): Response
    {
        $inicio = microtime(true);
        $response = $handler->handle($request);
        $ms = (microtime(true) - $inicio) * 1000;

        $linea = sprintf(
            '[%s] %s %s %d %.1fms',
            date('Y-m-d H:i:s'),
            $request->getMethod(),
            $request->getUri()->getPath(),
            $response->getStatusCode(),
            $ms
        );

        error_log($linea);                                                // consola
        file_put_contents($this->archivo, $linea . PHP_EOL, FILE_APPEND); // logs/app.log

        return $response;
    }
}
