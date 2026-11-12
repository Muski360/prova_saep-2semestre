<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\ConexaoBanco;
use PDO;

/**
 * Modelo de negocio para busca e autenticacao de usuarios.
 */
final class Usuario
{
    private static function pdo(): PDO
    {
        $cfg = dirname(__DIR__, 2) . '/config/database.ini';
        return ConexaoBanco::obterConexao($cfg);
    }

    public static function buscarPorEmail(string $email): ?array
    {
        $sql = "SELECT id, nome, email, senha_hash, perfil FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute([':email' => trim($email)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function buscarPorId(int $id): ?array
    {
        $sql = "SELECT id, nome, email, perfil FROM usuarios WHERE id = :id LIMIT 1";
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
