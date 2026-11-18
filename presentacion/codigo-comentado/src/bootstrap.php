<?php
/*
 * ============================================================================
 *  src/bootstrap.php  ->  ARMADO DE LA APLICACIÓN
 * ============================================================================
 *  Acá se "enchufa" todo en 5 pasos:
 *    1) Variables de entorno (.env) y zona horaria.
 *    2) Crear la app Slim y sus middlewares.
 *    3) Configurar las vistas (plantillas PHP con un layout común).
 *    4) Crear los objetos (inyección de dependencias):
 *         Database -> Repository -> Services -> Controllers
 *    5) Cargar las rutas (un archivo por módulo).
 *
 *  Idea clave para defender: INYECCIÓN DE DEPENDENCIAS.
 *    Ninguna clase hace "new" de lo que necesita: se lo pasamos desde afuera
 *    (por el constructor). Así cada capa no sabe cómo se crea la otra, y se
 *    puede cambiar una pieza sin tocar las demás.
 * ============================================================================
 */

// "use" importa las clases por su nombre completo (namespace) para poder
// escribir solo "EscaneoController" en vez de "App\controllers\EscaneoController".
use App\controllers\EscaneoController;
use App\controllers\HomeController;
use App\middlewares\LogMiddleware;
use App\persistence\Database;
use App\persistence\EscaneoRepository;
use App\services\EscaneoService;
use App\services\ScannerService;
use Dotenv\Dotenv;                              // librería que lee el archivo .env
use Slim\Factory\AppFactory;                    // fábrica que crea la app Slim
use Slim\Middleware\MethodOverrideMiddleware;   // permite PUT/DELETE desde formularios
use Slim\Views\PhpRenderer;                     // motor de vistas (plantillas .php)

// Autoload de Composer: carga automáticamente cualquier clase cuando se usa
// (sigue la regla PSR-4: App\ = carpeta src/). También carga src/helpers.php.
require __DIR__ . '/../vendor/autoload.php';

// ---------------------------------------------------------------------------
// 1) Variables de entorno (.env) y zona horaria
// ---------------------------------------------------------------------------
// $raiz = carpeta raíz del proyecto (un nivel arriba de src/).
$raiz = dirname(__DIR__);

// Lee el .env y llena $_ENV (DB_HOST, DB_USER, etc.).
// createImmutable: no pisa variables que ya existan.
// safeLoad: si no hay .env, NO tira error (usa los valores por defecto).
// ¿Por qué .env? Para no escribir contraseñas en el código ni subirlas a GitHub
// (.env está en .gitignore; se sube solo .env.example como modelo).
Dotenv::createImmutable($raiz)->safeLoad();

// Para que date() y NOW() muestren la hora de Argentina.
date_default_timezone_set('America/Argentina/Buenos_Aires');

// ---------------------------------------------------------------------------
// 2) Aplicación y middlewares
// ---------------------------------------------------------------------------
// Un MIDDLEWARE es una "capa" que envuelve a la app: la petición pasa por cada
// middleware antes de llegar al controlador, y la respuesta vuelve por ellos.
// En Slim el ÚLTIMO middleware agregado es el PRIMERO que se ejecuta (como una
// cebolla: la capa más externa es la última que pusimos).
$app = AppFactory::create();

// Decide qué ruta corresponde a la URL pedida.
$app->addRoutingMiddleware();

// Los formularios HTML solo saben hacer GET y POST. Este middleware mira un
// campo oculto llamado _METHOD (ej: value="DELETE") y convierte el POST en
// PUT o DELETE. Así podemos respetar los verbos REST del CRUD.
$app->add(new MethodOverrideMiddleware()); // campo oculto _METHOD -> PUT / DELETE

// Lee el cuerpo (body) de la petición: datos de formularios o JSON.
// Después el controlador los obtiene con $request->getParsedBody().
$app->addBodyParsingMiddleware();          // lee el body de formularios y JSON

// Captura cualquier error/excepción y muestra una página de error.
// 1er parámetro: mostrar detalles del error solo si APP_DEBUG=true (en
// desarrollo); en producción se pone false para no mostrar información interna.
// 2do y 3ro: registrar los errores en el log.
$app->addErrorMiddleware(($_ENV['APP_DEBUG'] ?? 'false') === 'true', true, true);

// Nuestro middleware propio: anota cada petición en logs/app.log.
// Es el último agregado => es el primero en ejecutarse => mide TODO.
$app->add(new LogMiddleware($raiz . '/logs/app.log')); // global, registra todo

// ---------------------------------------------------------------------------
// 3) Vistas con layout
// ---------------------------------------------------------------------------
// Las vistas son archivos .php con HTML que están en src/views.
$vista = new PhpRenderer(__DIR__ . '/views');
// layout.php es la "plantilla madre" (header, menú, estilos). Cada vista se
// inserta dentro del layout en la variable $content. Así no repetimos HTML.
$vista->setLayout('layout.php');

// ---------------------------------------------------------------------------
// 4) Dependencias: persistencia -> services -> controllers
// ---------------------------------------------------------------------------
// Una sola conexión a la base de datos para toda la petición.
$pdo = Database::conexion();

// El repositorio recibe la conexión: es el único que escribe SQL.
$escaneoRepo = new EscaneoRepository($pdo);

// El servicio del escáner recibe la ruta al script de Python y qué ejecutable
// usar (en Windows "python", en Linux suele ser "python3"; viene del .env).
$scanner = new ScannerService($raiz . '/scanner/escanear.py', $_ENV['PYTHON_BIN'] ?? 'python');

// El servicio de escaneos recibe: la conexión (para las transacciones),
// el repositorio (para el SQL) y el escáner (para escanear).
$escaneoService = new EscaneoService($pdo, $escaneoRepo, $scanner);

// Los controladores reciben la vista y, si hace falta, el servicio.
$homeController = new HomeController($vista);
$escaneoController = new EscaneoController($vista, $escaneoService);

// ---------------------------------------------------------------------------
// 5) Rutas, un archivo por módulo (TP13)
// ---------------------------------------------------------------------------
// Al hacer require, esos archivos "ven" las variables de acá ($app y los
// controladores) y registran sus rutas.
require __DIR__ . '/routes/home.routes.php';
require __DIR__ . '/routes/escaneos.routes.php';

// Devolvemos la app ya armada a public/index.php, que la ejecuta con run().
return $app;
