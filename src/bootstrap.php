<?php
// Arma la aplicación Slim: configuración, middlewares, dependencias y rutas.

use App\controllers\AuthController;
use App\controllers\EscaneoController;
use App\controllers\HomeController;
use App\middlewares\AuthMiddleware;
use App\middlewares\LogMiddleware;
use App\persistence\Database;
use App\persistence\EscaneoRepository;
use App\persistence\UsuarioRepository;
use App\services\AuthService;
use App\services\EscaneoService;
use App\services\ScannerService;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

// 1) Variables de entorno (.env)
$raiz = dirname(__DIR__);
Dotenv::createImmutable($raiz)->safeLoad();

date_default_timezone_set("America/Argentina/Buenos_Aires");

// 2) Sesión (para el login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3) Aplicación y middlewares
//    En Slim el último middleware agregado es el primero que se ejecuta.
$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->add(new MethodOverrideMiddleware()); // campo oculto _METHOD -> PUT / DELETE
$app->addBodyParsingMiddleware();          // lee el body de formularios y JSON
$app->addErrorMiddleware(($_ENV['APP_DEBUG'] ?? 'false') === 'true', true, true);
$app->add(new LogMiddleware($raiz . '/logs/app.log')); // global, registra todo

// 4) Vistas con layout
$vista = new PhpRenderer(__DIR__ . '/views');
$vista->setLayout('layout.php');

// 5) Dependencias: persistencia -> services -> controllers
$pdo = Database::conexion();
$usuarioRepo = new UsuarioRepository($pdo);
$escaneoRepo = new EscaneoRepository($pdo);

$authService = new AuthService($usuarioRepo);
$scanner = new ScannerService($raiz . '/scanner/escanear.py', $_ENV['PYTHON_BIN'] ?? 'python');
$escaneoService = new EscaneoService($pdo, $escaneoRepo, $scanner);

$homeController = new HomeController($vista);
$authController = new AuthController($vista, $authService);
$escaneoController = new EscaneoController($vista, $escaneoService);
$authMiddleware = new AuthMiddleware();

// 6) Rutas, un archivo por módulo (TP13)
require __DIR__ . '/routes/home.routes.php';
require __DIR__ . '/routes/auth.routes.php';
require __DIR__ . '/routes/escaneos.routes.php';

return $app;
