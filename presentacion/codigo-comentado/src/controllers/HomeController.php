<?php
/*
 * ============================================================================
 *  src/controllers/HomeController.php  ->  CONTROLADOR DE LA LANDING
 * ============================================================================
 *  Capa: CONTROLADOR. Recibe la petición y decide qué vista mostrar.
 *  La landing es pública y usa datos de EJEMPLO (mock): no consulta la base
 *  ni escanea nada; solo muestra cómo se ve un resultado.
 * ============================================================================
 */

// namespace = "carpeta lógica" de la clase. Coincide con src/controllers
// (regla PSR-4 de Composer: App\ = src/).
namespace App\controllers;

// Interfaces estándar PSR-7 para la petición y la respuesta HTTP.
// "as Response" / "as Request" = alias más cortos.
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

// Landing pública con datos de ejemplo (mock), no necesita login.
class HomeController
{
    // "Constructor promotion" de PHP 8: escribir "private PhpRenderer $vista"
    // en el constructor declara la propiedad $this->vista y la asigna sola.
    // La vista se recibe desde afuera (inyección de dependencias).
    public function __construct(private PhpRenderer $vista)
    {
    }

    // GET /  -> Slim llama a este método con la petición y una respuesta vacía.
    // Todo método de controlador DEVUELVE una Response.
    public function index(Request $request, Response $response): Response
    {
        // Array de ejemplo con 4 puertos típicos, uno por cada nivel de riesgo.
        $ejemplo = [
            ['puerto' => 23,   'servicio' => 'telnet', 'riesgo' => 'alto',  'explicacion' => 'Envía contraseñas sin cifrar.'],
            ['puerto' => 3389, 'servicio' => 'rdp',    'riesgo' => 'alto',  'explicacion' => 'Escritorio remoto: blanco común de ataques.'],
            ['puerto' => 22,   'servicio' => 'ssh',    'riesgo' => 'medio', 'explicacion' => 'Seguro, pero conviene usar claves fuertes.'],
            ['puerto' => 443,  'servicio' => 'https',  'riesgo' => 'bajo',  'explicacion' => 'Web cifrada, normal en un servidor.'],
        ];

        // render(respuesta, archivo de vista, variables para la vista).
        // Dentro de views/index.php van a existir $titulo y $ejemplo.
        return $this->vista->render($response, 'index.php', [
            'titulo' => 'TraffiCop',
            'ejemplo' => $ejemplo,
        ]);
    }
}
