<?php
namespace App\controllers;

use App\services\EscaneoService;
use App\services\ScannerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use Slim\Views\PhpRenderer;

// CRUD de escaneos. Valida lo que llega, llama al service y elige la vista.
class EscaneoController
{
    private const MAX_NOTAS = 255;

    public function __construct(private PhpRenderer $vista, private EscaneoService $escaneos)
    {
    }

    // GET /escaneos?limit=N
    public function index(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();
        $limite = null;

        if (isset($query['limit'])) {
            $limite = filter_var($query['limit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($limite === false) {
                return $this->vista->render($response->withStatus(400), 'escaneos/index.php', [
                    'titulo' => 'Mis escaneos',
                    'escaneos' => [],
                    'limite' => null,
                    'error' => 'El parámetro limit debe ser un número entero positivo',
                ]);
            }
        }

        return $this->vista->render($response, 'escaneos/index.php', [
            'titulo' => 'Mis escaneos',
            'escaneos' => $this->escaneos->listar($this->usuarioId(), $limite),
            'limite' => $limite,
        ]);
    }

    // GET /escaneos/create
    public function create(Request $request, Response $response): Response
    {
        return $this->vista->render($response, 'escaneos/create.php', ['titulo' => 'Nuevo escaneo']);
    }

    // POST /escaneos -> ejecuta el escaneo, lo guarda y muestra datos enviados + resultado
    public function store(Request $request, Response $response): Response
    {
        $datos = (array) $request->getParsedBody();
        $objetivo = trim($datos['objetivo'] ?? '');
        $notas = trim($datos['notas'] ?? '');

        $error = null;
        if (!ScannerService::ipPermitida($objetivo)) {
            $error = 'Solo se pueden escanear IPs privadas (10.x, 172.16-31.x, 192.168.x) o 127.x';
        } elseif (mb_strlen($notas) > self::MAX_NOTAS) {
            $error = 'Las notas pueden tener como máximo ' . self::MAX_NOTAS . ' caracteres';
        }

        if ($error === null) {
            try {
                $id = $this->escaneos->crear($this->usuarioId(), $objetivo, $notas === '' ? null : $notas);
                return $this->vista->render($response->withStatus(201), 'escaneos/show.php', [
                    'titulo' => 'Resultado del escaneo',
                    'escaneo' => $this->escaneos->ver($id, $this->usuarioId()),
                    'enviado' => ['objetivo' => $objetivo, 'notas' => $notas],
                ]);
            } catch (RuntimeException $e) {
                $error = 'No se pudo escanear: ' . $e->getMessage();
            }
        }

        return $this->vista->render($response->withStatus(422), 'escaneos/create.php', [
            'titulo' => 'Nuevo escaneo',
            'error' => $error,
            'viejo' => ['objetivo' => $objetivo, 'notas' => $notas],
        ]);
    }

    // GET /escaneos/{id}
    public function show(Request $request, Response $response, array $args): Response
    {
        $escaneo = $this->escaneos->ver((int) $args['id'], $this->usuarioId());
        if ($escaneo === null) {
            return $this->noEncontrado($response);
        }
        return $this->vista->render($response, 'escaneos/show.php', [
            'titulo' => 'Escaneo #' . $escaneo['id'],
            'escaneo' => $escaneo,
        ]);
    }

    // GET /escaneos/{id}/edit
    public function edit(Request $request, Response $response, array $args): Response
    {
        $escaneo = $this->escaneos->ver((int) $args['id'], $this->usuarioId());
        if ($escaneo === null) {
            return $this->noEncontrado($response);
        }
        return $this->vista->render($response, 'escaneos/update.php', [
            'titulo' => 'Editar notas',
            'escaneo' => $escaneo,
        ]);
    }

    // PUT /escaneos/{id} -> edita las notas (máximo 255 caracteres)
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $escaneo = $this->escaneos->ver($id, $this->usuarioId());
        if ($escaneo === null) {
            return $this->noEncontrado($response);
        }

        $notas = trim(((array) $request->getParsedBody())['notas'] ?? '');
        if (mb_strlen($notas) > self::MAX_NOTAS) {
            $escaneo['notas'] = $notas;
            return $this->vista->render($response->withStatus(422), 'escaneos/update.php', [
                'titulo' => 'Editar notas',
                'escaneo' => $escaneo,
                'error' => 'Las notas pueden tener como máximo ' . self::MAX_NOTAS . ' caracteres',
            ]);
        }

        $this->escaneos->actualizarNotas($id, $this->usuarioId(), $notas === '' ? null : $notas);
        return $response->withHeader('Location', '/escaneos/' . $id)->withStatus(302);
    }

    // DELETE /escaneos/{id}
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        if ($this->escaneos->ver($id, $this->usuarioId()) === null) {
            return $this->noEncontrado($response);
        }
        $this->escaneos->borrar($id, $this->usuarioId());
        return $response->withHeader('Location', '/escaneos')->withStatus(302);
    }

    private function noEncontrado(Response $response): Response
    {
        return $this->vista->render($response->withStatus(404), 'escaneos/not_found.php', [
            'titulo' => 'Escaneo no encontrado',
        ]);
    }

    private function usuarioId(): int
    {
        return (int) $_SESSION['usuario']['id'];
    }
}
