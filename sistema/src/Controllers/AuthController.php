<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Usuario;

/**
 * Controller de login, controle de sessao e encerramento de acesso.
 */
final class AuthController extends Controller
{
    //renderiza o formulario de login
    public function formulario(): void
    {
        $expirada = isset($_GET['expirada']) ? 'Sua sessão expirou por inatividade. Faça login novamente.' : null;
        $this->renderizar('auth/login', [
            'titulo'    => 'Login - Clínica Veterinária SupVet',
            'erro'      => $expirada,
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }
    //valido o login
    public function login(): void
    {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->renderizar('auth/login', ['titulo' => 'Login', 'erro' => 'Token CSRF inválido ou expirado.', 'csrfToken' => $this->obterCsrfToken()]);
            return;
        }
        $email = trim($_POST['email'] ?? '');
        $senha = (string)($_POST['senha'] ?? '');
        $usuario = Usuario::buscarPorEmail($email);

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']     = (int)$usuario['id'];
            $_SESSION['usuario_nome']   = $usuario['nome'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];
            $_SESSION['ultimo_acesso']  = time();
            $this->redirecionar('/');
        }
        $this->renderizar('auth/login', [
            'titulo'    => 'Login',
            'erro'      => 'Falha na autenticação: E-mail ou senha incorretos.',
            'csrfToken' => $this->obterCsrfToken()
        ]);
    }

    public function logout(): void
    {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->mensagem('Formulário expirado. Tente novamente.');
            $this->redirecionar('/');
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $this->redirecionar('/login');
    }
}
