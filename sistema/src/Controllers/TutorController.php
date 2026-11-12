<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Tutor;
use App\Core\Validacao;

final class TutorController extends Controller
{
    public function listar(): void
    {
        $busca = trim($_GET['q'] ?? '');
        $this->renderizar('tutores/listar', ['titulo' => 'Tutores', 'tutores' => Tutor::listar($busca), 'busca' => $busca]);
    }

    public function formulario(): void
    {
        $this->renderizar('tutores/formulario', ['titulo' => 'Novo tutor', 'tutor' => []]);
    }

    public function formularioEdicao(): void
    {
        $tutor = Tutor::buscarPorId((int)($_GET['id'] ?? 0));
        if (!$tutor) {
            $this->mensagem('Tutor não encontrado.');
            $this->redirecionar('/tutores');
        }
        $this->renderizar('tutores/formulario', ['titulo' => 'Editar tutor', 'tutor' => $tutor]);
    }

    public function salvar(): void { $this->gravar(false); }
    public function atualizar(): void { $this->gravar(true); }

    private function gravar(bool $editar): void
    {
        $tutor = [
            'id' => (int)($_POST['id'] ?? 0), 'nome' => trim($_POST['nome'] ?? ''),
            'cpf' => preg_replace('/\D/', '', $_POST['cpf'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''), 'email' => trim($_POST['email'] ?? '')
        ];
        $erro = '';
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $erro = 'Formulário expirado. Tente novamente.';
        } elseif ($tutor['nome'] === '' || mb_strlen($tutor['nome']) > 128 || !Validacao::cpf($tutor['cpf']) ||
            !preg_match('/^\d{10,11}$/', preg_replace('/\D/', '', $tutor['telefone'])) ||
            !filter_var($tutor['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($tutor['email']) > 128 || mb_strlen($tutor['telefone']) > 128) {
            $erro = 'Informe nome, CPF válido, telefone com DDD e e-mail válido (até 128 caracteres).';
        } elseif ($editar && !Tutor::buscarPorId($tutor['id'])) {
            $erro = 'Tutor não encontrado.';
        }
        if ($erro !== '') {
            $this->renderizar('tutores/formulario', ['titulo' => $editar ? 'Editar tutor' : 'Novo tutor', 'tutor' => $tutor, 'erro' => $erro]);
            return;
        }
        if ($editar) {
            Tutor::atualizar($tutor['id'], $tutor['nome'], $tutor['cpf'], $tutor['telefone'], $tutor['email']);
        } else {
            Tutor::salvar($tutor['nome'], $tutor['cpf'], $tutor['telefone'], $tutor['email']);
        }
        $this->mensagem('Tutor salvo.');
        $this->redirecionar('/tutores');
    }

    public function excluir(): void
    {
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->mensagem('Formulário expirado. Tente novamente.');
        } else {
            try {
                $ok = Tutor::excluir((int)($_POST['id'] ?? 0));
                $this->mensagem($ok ? 'Tutor excluído.' : 'Tutor não encontrado.');
            } catch (\PDOException $e) {
                if (!in_array($e->getCode(), ['23503', '23001'], true)) { throw $e; }
                $this->mensagem('Não é possível excluir um tutor com pets vinculados.');
            }
        }
        $this->redirecionar('/tutores');
    }
}
