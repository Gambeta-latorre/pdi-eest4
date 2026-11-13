<?php
namespace App\controllers;

use App\services\AuthService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

// Registro, login y logout. Valida los datos del formulario y responde.
class AuthController
{
    public function __construct(private PhpRenderer $vista, private AuthService $auth)
    {
    }

    public function mostrarRegistro(Request $request, Response $response): Response
    {
        return $this->vista->render($response, 'auth/register.php', ['titulo' => 'Crear cuenta']);
    }

    public function registrar(Request $request, Response $response): Response
    {
        $datos = (array) $request->getParsedBody();
        $nombre = trim($datos['nombre'] ?? '');
        $email = trim($datos['email'] ?? '');
        $password = $datos['password'] ?? '';

        $error = null;
        if ($nombre === '' || $email === '' || $password === '') {
            $error = 'Completá todos los campos';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El email no es válido';
        } elseif (strlen($password) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres';
        }

        if ($error === null) {
            try {
                $usuario = $this->auth->registrar($nombre, $email, $password);
                $this->iniciarSesion($usuario);
                return $this->redirigir($response, '/escaneos');
            } catch (InvalidArgumentException $e) {
                $error = $e->getMessage();
            }
        }

        return $this->vista->render($response->withStatus(422), 'auth/register.php', [
            'titulo' => 'Crear cuenta',
            'error' => $error,
            'viejo' => ['nombre' => $nombre, 'email' => $email],
        ]);
    }

    public function mostrarLogin(Request $request, Response $response): Response
    {
        return $this->vista->render($response, 'auth/login.php', ['titulo' => 'Iniciar sesión']);
    }

    public function login(Request $request, Response $response): Response
    {
        $datos = (array) $request->getParsedBody();
        $email = trim($datos['email'] ?? '');
        $password = $datos['password'] ?? '';

        $usuario = $this->auth->login($email, $password);
        if ($usuario === null) {
            return $this->vista->render($response->withStatus(401), 'auth/login.php', [
                'titulo' => 'Iniciar sesión',
                'error' => 'Email o contraseña incorrectos',
                'viejo' => ['email' => $email],
            ]);
        }

        $this->iniciarSesion($usuario);
        return $this->redirigir($response, '/escaneos');
    }

    public function logout(Request $request, Response $response): Response
    {
        $_SESSION = [];
        session_destroy();
        return $this->redirigir($response, '/');
    }

    private function iniciarSesion(array $usuario): void
    {
        session_regenerate_id(true); // nuevo id de sesión al loguearse (evita robo de sesión)
        $_SESSION['usuario'] = $usuario;
    }

    private function redirigir(Response $response, string $url): Response
    {
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
