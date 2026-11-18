<?php
/*
 * ============================================================================
 *  src/controllers/EscaneoController.php  ->  CONTROLADOR DEL CRUD DE ESCANEOS
 * ============================================================================
 *  Capa: CONTROLADOR. Su trabajo es SOLO:
 *    1) Leer y VALIDAR lo que manda el usuario (URL, formulario).
 *    2) Pedirle al SERVICE que haga el trabajo.
 *    3) Elegir la VISTA y el CÓDIGO DE ESTADO HTTP de la respuesta.
 *  El controlador NO escribe SQL ni ejecuta Python: eso es de otras capas.
 *
 *  Códigos HTTP que usamos (pregunta muy típica):
 *    200 OK            -> todo bien (es el valor por defecto).
 *    201 Created       -> se creó un escaneo nuevo.
 *    302 Found         -> redirección (después de editar o borrar).
 *    400 Bad Request   -> el parámetro ?limit está mal escrito.
 *    404 Not Found     -> el escaneo con ese id no existe.
 *    422 Unprocessable -> el formulario tiene datos inválidos.
 * ============================================================================
 */
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
    // Constante: largo máximo de las notas. Coincide con la columna
    // notas VARCHAR(255) de la base (migración 006). Validamos ANTES de
    // llegar a la base para dar un mensaje claro al usuario.
    private const MAX_NOTAS = 255;

    // Recibe la vista y el servicio por inyección de dependencias.
    public function __construct(private PhpRenderer $vista, private EscaneoService $escaneos)
    {
    }

    // ------------------------------------------------------------------------
    // GET /escaneos?limit=N  -> LISTADO (READ)
    // ------------------------------------------------------------------------
    public function index(Request $request, Response $response): Response
    {
        // Parámetros de la URL después del "?" (query string), como array.
        $query = $request->getQueryParams();
        $limite = null; // null = sin límite, mostrar todos

        if (isset($query['limit'])) {
            // filter_var con FILTER_VALIDATE_INT devuelve el número si es un
            // entero válido >= 1 (min_range), o false si no ("abc", "0", "-3").
            $limite = filter_var($query['limit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($limite === false) {
                // Dato inválido -> 400 Bad Request y mensaje de error.
                return $this->vista->render($response->withStatus(400), 'escaneos/index.php', [
                    'titulo' => 'Historial',
                    'escaneos' => [],
                    'limite' => null,
                    'error' => 'El parámetro limit debe ser un número entero positivo',
                ]);
            }
        }

        // Todo bien -> 200 con la lista que nos da el service.
        return $this->vista->render($response, 'escaneos/index.php', [
            'titulo' => 'Historial',
            'escaneos' => $this->escaneos->listar($limite),
            'limite' => $limite,
        ]);
    }

    // ------------------------------------------------------------------------
    // GET /escaneos/create  -> FORMULARIO PARA UN ESCANEO NUEVO
    // ------------------------------------------------------------------------
    public function create(Request $request, Response $response): Response
    {
        return $this->vista->render($response, 'escaneos/create.php', ['titulo' => 'Nuevo escaneo']);
    }

    // ------------------------------------------------------------------------
    // POST /escaneos  -> CREAR (CREATE)
    // Ejecuta el escaneo, lo guarda y muestra datos enviados + resultado.
    // ------------------------------------------------------------------------
    public function store(Request $request, Response $response): Response
    {
        // Datos del formulario (los leyó el BodyParsingMiddleware).
        // (array) asegura que sea un array aunque venga vacío/null.
        $datos = (array) $request->getParsedBody();
        // trim() saca espacios al principio y al final.
        // ?? '' = si el campo no vino, usar texto vacío (operador null coalescing).
        $objetivo = trim($datos['objetivo'] ?? '');
        $notas = trim($datos['notas'] ?? '');

        // VALIDACIÓN EN EL SERVIDOR. Aunque el HTML tenga "required" y
        // "maxlength", eso se puede saltear fácil (ej: con las herramientas del
        // navegador). La validación que vale es la del servidor.
        $error = null;
        if (!ScannerService::ipPermitida($objetivo)) {
            // Seguridad y ética: solo dejamos escanear la red local o la propia
            // PC. Escanear equipos ajenos de Internet sin permiso no está bien.
            $error = 'Solo se pueden escanear IPs privadas (10.x, 172.16-31.x, 192.168.x) o 127.x';
        } elseif (mb_strlen($notas) > self::MAX_NOTAS) {
            // mb_strlen cuenta CARACTERES (una "ñ" o "á" = 1); strlen contaría bytes.
            $error = 'Las notas pueden tener como máximo ' . self::MAX_NOTAS . ' caracteres';
        }

        if ($error === null) {
            try {
                // El service escanea y guarda. Notas vacías se guardan como NULL.
                // Devuelve el id del nuevo escaneo.
                $id = $this->escaneos->crear($objetivo, $notas === '' ? null : $notas);
                // 201 Created + mostramos lo que mandó el usuario y el resultado.
                return $this->vista->render($response->withStatus(201), 'escaneos/show.php', [
                    'titulo' => 'Resultado del escaneo',
                    'escaneo' => $this->escaneos->ver($id),
                    'enviado' => ['objetivo' => $objetivo, 'notas' => $notas],
                ]);
            } catch (RuntimeException $e) {
                // Si falló el escáner (no está Python, JSON inválido, etc.)
                // no se rompe la página: mostramos el error en el formulario.
                $error = 'No se pudo escanear: ' . $e->getMessage();
            }
        }

        // Si llegamos acá hubo un error -> 422 y volvemos a mostrar el
        // formulario con los datos que había escrito ("viejo") para no perderlos.
        return $this->vista->render($response->withStatus(422), 'escaneos/create.php', [
            'titulo' => 'Nuevo escaneo',
            'error' => $error,
            'viejo' => ['objetivo' => $objetivo, 'notas' => $notas],
        ]);
    }

    // ------------------------------------------------------------------------
    // GET /escaneos/{id}  -> DETALLE (READ uno)
    // ------------------------------------------------------------------------
    // $args trae los parámetros de la ruta: $args['id'] viene de {id}.
    public function show(Request $request, Response $response, array $args): Response
    {
        // (int) convierte el texto "5" en el número 5.
        $escaneo = $this->escaneos->ver((int) $args['id']);
        if ($escaneo === null) {
            return $this->noEncontrado($response); // 404
        }
        return $this->vista->render($response, 'escaneos/show.php', [
            'titulo' => 'Escaneo #' . $escaneo['id'],
            'escaneo' => $escaneo,
        ]);
    }

    // ------------------------------------------------------------------------
    // GET /escaneos/{id}/edit  -> FORMULARIO PARA EDITAR NOTAS
    // ------------------------------------------------------------------------
    public function edit(Request $request, Response $response, array $args): Response
    {
        $escaneo = $this->escaneos->ver((int) $args['id']);
        if ($escaneo === null) {
            return $this->noEncontrado($response);
        }
        return $this->vista->render($response, 'escaneos/update.php', [
            'titulo' => 'Editar notas',
            'escaneo' => $escaneo,
        ]);
    }

    // ------------------------------------------------------------------------
    // PUT /escaneos/{id}  -> EDITAR (UPDATE). Solo se editan las notas.
    // ¿Por qué solo notas? Porque el resultado de un escaneo es un hecho:
    // cambiar puertos o la IP sería falsear el registro.
    // ------------------------------------------------------------------------
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $escaneo = $this->escaneos->ver($id);
        if ($escaneo === null) {
            return $this->noEncontrado($response);
        }

        $notas = trim(((array) $request->getParsedBody())['notas'] ?? '');
        if (mb_strlen($notas) > self::MAX_NOTAS) {
            // Le devolvemos al formulario lo que escribió, con el error.
            $escaneo['notas'] = $notas;
            return $this->vista->render($response->withStatus(422), 'escaneos/update.php', [
                'titulo' => 'Editar notas',
                'escaneo' => $escaneo,
                'error' => 'Las notas pueden tener como máximo ' . self::MAX_NOTAS . ' caracteres',
            ]);
        }

        $this->escaneos->actualizarNotas($id, $notas === '' ? null : $notas);

        // Patrón PRG (Post/Redirect/Get): después de modificar algo, redirigimos
        // con 302 al detalle. Así, si el usuario apreta F5, no reenvía el formulario.
        return $response->withHeader('Location', '/escaneos/' . $id)->withStatus(302);
    }

    // ------------------------------------------------------------------------
    // DELETE /escaneos/{id}  -> BORRAR (DELETE)
    // ------------------------------------------------------------------------
    public function destroy(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        if ($this->escaneos->ver($id) === null) {
            return $this->noEncontrado($response);
        }
        // Los puertos de ese escaneo se borran solos (ON DELETE CASCADE).
        $this->escaneos->borrar($id);
        // Redirigimos al listado (mismo patrón PRG).
        return $response->withHeader('Location', '/escaneos')->withStatus(302);
    }

    // Método privado reutilizable: evita repetir el mismo código de 404 en
    // show, edit, update y destroy (principio DRY: "no te repitas").
    private function noEncontrado(Response $response): Response
    {
        return $this->vista->render($response->withStatus(404), 'escaneos/not_found.php', [
            'titulo' => 'Escaneo no encontrado',
        ]);
    }
}
