<?php
/*
 * ============================================================================
 *  public/index.php  ->  PUNTO DE ENTRADA (Front Controller)
 * ============================================================================
 *  ¿Qué es?
 *    Es el ÚNICO archivo PHP que el navegador puede "tocar". El servidor web
 *    (php -S localhost:8000 -t public) solo publica la carpeta /public, así que
 *    TODAS las peticiones (/, /escaneos, /escaneos/5, etc.) entran por acá.
 *
 *  ¿Por qué así?
 *    - Seguridad: el resto del código (src/, .env, vendor/) queda FUERA de la
 *      carpeta pública, nadie lo puede descargar desde el navegador.
 *    - Orden: hay un solo lugar donde arranca la app. Este patrón se llama
 *      "Front Controller".
 *
 *  Pregunta típica: "¿Por qué es tan corto?"
 *    -> Porque solo delega: todo el armado está en src/bootstrap.php.
 * ============================================================================
 */

// __DIR__ = carpeta donde está este archivo (public/).
// "require" ejecuta bootstrap.php, que arma la app Slim y la DEVUELVE (return $app).
$app = require __DIR__ . '/../src/bootstrap.php';

// run() lee la petición HTTP actual, la pasa por los middlewares, busca la ruta
// que corresponde, ejecuta el controlador y manda la respuesta al navegador.
$app->run();
