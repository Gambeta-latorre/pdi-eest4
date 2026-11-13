<?php
namespace App\persistence;

use PDO;

// Única clase que habla con la tabla "usuarios".
class UsuarioRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function crear(string $nombre, string $email, string $hash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)'
        );
        $stmt->execute([$nombre, $email, $hash]);
        return (int) $this->pdo->lastInsertId();
    }
}
