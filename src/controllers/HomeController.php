<?php
namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

// Landing pública con datos de ejemplo (mock), no necesita login.
class HomeController
{
    public function __construct(private PhpRenderer $vista)
    {
    }

    public function index(Request $request, Response $response): Response
    {
        $ejemplo = [
            ['puerto' => 23,   'servicio' => 'telnet', 'riesgo' => 'alto',  'explicacion' => 'Envía contraseñas sin cifrar.'],
            ['puerto' => 3389, 'servicio' => 'rdp',    'riesgo' => 'alto',  'explicacion' => 'Escritorio remoto: blanco común de ataques.'],
            ['puerto' => 22,   'servicio' => 'ssh',    'riesgo' => 'medio', 'explicacion' => 'Seguro, pero conviene usar claves fuertes.'],
            ['puerto' => 443,  'servicio' => 'https',  'riesgo' => 'bajo',  'explicacion' => 'Web cifrada, normal en un servidor.'],
        ];

        return $this->vista->render($response, 'index.php', [
            'titulo' => 'TraffiCop',
            'ejemplo' => $ejemplo,
        ]);
    }
}
