<?php
// Módulo escaneos: CRUD completo.
use Slim\Routing\RouteCollectorProxy;

$app->group('/escaneos', function (RouteCollectorProxy $grupo) use ($escaneoController) {
    $grupo->get('', [$escaneoController, 'index']);                 // listado (?limit=N)
    $grupo->get('/create', [$escaneoController, 'create']);         // formulario nuevo
    $grupo->post('', [$escaneoController, 'store']);                // ejecuta y guarda
    $grupo->get('/{id:[0-9]+}', [$escaneoController, 'show']);      // detalle
    $grupo->get('/{id:[0-9]+}/edit', [$escaneoController, 'edit']); // formulario notas
    $grupo->put('/{id:[0-9]+}', [$escaneoController, 'update']);    // edita notas
    $grupo->delete('/{id:[0-9]+}', [$escaneoController, 'destroy']); // borra
});
