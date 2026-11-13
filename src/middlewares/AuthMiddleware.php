<?php
namespace App\middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as NuevaRespuesta;

// Si no hay sesión iniciada, redirige al login. Si la hay, deja pasar la petición.
class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        if (empty($_SESSION['usuario'])) {
            return (new NuevaRespuesta())
                ->withHeader('Location', '/auth/login')
                ->withStatus(302);
        }
        return $handler->handle($request);
    }
}
