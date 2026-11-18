<?php
/*
 * ============================================================================
 *  src/routes/home.routes.php  ->  RUTAS DEL MÓDULO "HOME"
 * ============================================================================
 *  Una RUTA une: VERBO HTTP + URL  ->  método de un controlador.
 *  Este archivo se carga con require desde bootstrap.php, por eso puede usar
 *  $app y $homeController sin declararlos acá.
 *  (TP13: un archivo de rutas por módulo.)
 * ============================================================================
 */

// GET /  -> la landing pública (página de inicio).
// [$homeController, 'index'] = "llamá al método index del objeto $homeController".
$app->get('/', [$homeController, 'index']);
