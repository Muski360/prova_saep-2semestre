<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Guard para protecao de rotas autenticadas e verificacao de expiracao de sessao.
 */
final class AuthGuard
{
    public static function proteger(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!self::usuarioLogado()) {
            header("Location: /login");
            exit;
        }
        if (self::sessaoExpirada()) {
            self::encerrarSessaoPorExpiracao();
        }
        $_SESSION['ultimo_acesso'] = time();
    }

    public static function usuarioLogado(): bool
    {
        return !empty($_SESSION['usuario_id']);
    }

    private static function sessaoExpirada(): bool
    {
        $cfg = parse_ini_file(dirname(__DIR__, 2) . '/config/database.ini', true);
        $limite = (int)($cfg['security']['session_timeout'] ?? 900);
        $ultimoAcesso = $_SESSION['ultimo_acesso'] ?? time();
        return (time() - $ultimoAcesso) > $limite;
    }

    private static function encerrarSessaoPorExpiracao(): void
    {
        $_SESSION = [];
        session_destroy();
        header("Location: /login?expirada=1");
        exit;
    }
}
