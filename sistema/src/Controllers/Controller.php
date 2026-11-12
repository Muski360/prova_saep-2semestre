<?php

declare(strict_types=1);

namespace App\Controllers;

use RuntimeException;

abstract class Controller
{

    protected function renderizar(string $view, array $dados = []): void
    {
        $csrfToken = $this->obterCsrfToken();
        extract($dados, EXTR_SKIP);
        $arquivoView = dirname(__DIR__) . "/Views/{$view}.php";
        if (!file_exists($arquivoView)) {
            throw new RuntimeException("A view nao foi encontrada: {$arquivoView}");
        }
        require_once dirname(__DIR__) . "/Views/layout/header.php";
        require_once $arquivoView;
        require_once dirname(__DIR__) . "/Views/layout/footer.php";
    }

    protected function mensagem(string $texto): void
    {
        $_SESSION['mensagem'] = $texto;
    }

    protected function redirecionar(string $rota): void
    {
        header("Location: {$rota}");
        exit;
    }

    protected function obterCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['csrf_token'];
    }

    protected function validarCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessaoToken = $_SESSION['csrf_token'] ?? '';
        return !empty($token) && hash_equals((string)$sessaoToken, (string)$token);
    }
}
