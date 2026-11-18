<?php
/*
 * ============================================================================
 *  src/routes/escaneos.routes.php  ->  RUTAS DEL MÓDULO "ESCANEOS" (CRUD)
 * ============================================================================
 *  CRUD = Create (crear), Read (leer), Update (editar), Delete (borrar).
 *  Seguimos la convención REST: la URL nombra el RECURSO (/escaneos) y el
 *  VERBO HTTP dice qué hacemos con él.
 *
 *    Verbo   URL                    Método       ¿Qué hace?
 *    -----   --------------------   ----------   ------------------------------
 *    GET     /escaneos              index        Lista (READ todos) ?limit=N
 *    GET     /escaneos/create       create       Muestra el formulario nuevo
 *    POST    /escaneos              store        Escanea y guarda (CREATE)
 *    GET     /escaneos/{id}         show         Detalle de uno (READ uno)
 *    GET     /escaneos/{id}/edit    edit         Formulario para editar notas
 *    PUT     /escaneos/{id}         update       Guarda las notas (UPDATE)
 *    DELETE  /escaneos/{id}         destroy      Borra (DELETE)
 *
 *  PUT y DELETE llegan desde formularios gracias al campo oculto _METHOD
 *  (MethodOverrideMiddleware en bootstrap.php).
 * ============================================================================
 */

// RouteCollectorProxy es el tipo del objeto $grupo que Slim nos pasa.
use Slim\Routing\RouteCollectorProxy;

// group('/escaneos', ...) = todas las rutas de adentro empiezan con /escaneos.
// "use ($escaneoController)" deja que la función anónima vea esa variable de afuera.
$app->group('/escaneos', function (RouteCollectorProxy $grupo) use ($escaneoController) {
    $grupo->get('', [$escaneoController, 'index']);                 // listado (?limit=N)
    $grupo->get('/create', [$escaneoController, 'create']);         // formulario nuevo
    $grupo->post('', [$escaneoController, 'store']);                // ejecuta y guarda

    // {id:[0-9]+} = parámetro "id" que SOLO acepta dígitos (expresión regular).
    // Si alguien pide /escaneos/abc, ni siquiera entra al controlador: da 404.
    // Además evita que "create" se confunda con un id.
    $grupo->get('/{id:[0-9]+}', [$escaneoController, 'show']);      // detalle
    $grupo->get('/{id:[0-9]+}/edit', [$escaneoController, 'edit']); // formulario notas
    $grupo->put('/{id:[0-9]+}', [$escaneoController, 'update']);    // edita notas
    $grupo->delete('/{id:[0-9]+}', [$escaneoController, 'destroy']); // borra
});
