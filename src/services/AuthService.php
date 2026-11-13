<?php
namespace App\services;

use App\persistence\UsuarioRepository;
use InvalidArgumentException;

// Lógica de registro y login.
class AuthService
{
    public function __construct(private UsuarioRepository $usuarios)
    {
    }

    public function registrar(string $nombre, string $email, string $password): array
    {
        if ($this->usuarios->buscarPorEmail($email)) {
            throw new InvalidArgumentException('Ya existe una cuenta con ese email');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $id = $this->usuarios->crear($nombre, $email, $hash);
        return ['id' => $id, 'nombre' => $nombre, 'email' => $email];
    }

    // Devuelve el usuario si email y contraseña coinciden, o null si no.
    public function login(string $email, string $password): ?array
    {
        $usuario = $this->usuarios->buscarPorEmail($email);
        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            return null;
        }
        return ['id' => (int) $usuario['id'], 'nombre' => $usuario['nombre'], 'email' => $usuario['email']];
    }
}
