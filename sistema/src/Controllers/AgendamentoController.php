<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Agendamento;
use App\Models\Pet;
use App\Core\Validacao;

final class AgendamentoController extends Controller
{
    public function listar(): void
    {
        $this->renderizar('agendamentos/listar', ['titulo' => 'Agendamentos por data', 'agendamentos' => Agendamento::listarPorData()]);
    }
    public function formulario(): void
    {
        $this->renderizar('agendamentos/criar', ['titulo' => 'Novo agendamento', 'pets' => Pet::listarTodos()]);
    }
    public function salvar(): void
    {
        $pet = (int)($_POST['pet_id'] ?? 0);
        $data = trim($_POST['data_agendamento'] ?? '');
        $hora = trim($_POST['hora_agendamento'] ?? '');
        $servico = trim($_POST['servico'] ?? '');
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null) || !Pet::buscarPorId($pet) ||
            !Validacao::data($data) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora) || $servico === '' || mb_strlen($servico) > 128) {
            $this->renderizar('agendamentos/criar', ['titulo' => 'Novo agendamento', 'pets' => Pet::listarTodos(), 'erro' => 'Selecione um pet e informe data, hora e serviço válidos.', 'dados' => $_POST]);
            return;
        }
        Agendamento::salvar($pet, (int)$_SESSION['usuario_id'], $data, $hora, $servico);
        $this->mensagem('Agendamento salvo.');
        $this->redirecionar('/agendamentos');
    }
    public function formularioEditarData(): void
    {
        $agendamento = Agendamento::buscarPorId((int)($_GET['id'] ?? 0));
        if (!$agendamento) {
            $this->mensagem('Agendamento não encontrado.');
            $this->redirecionar('/agendamentos');
        }
        $this->renderizar('agendamentos/editar-data', ['titulo' => 'Alterar data e hora', 'agendamento' => $agendamento]);
    }
    public function atualizarData(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $data = trim($_POST['data_agendamento'] ?? '');
        $hora = trim($_POST['hora_agendamento'] ?? '');
        $agendamento = Agendamento::buscarPorId($id);
        if (!$agendamento) {
            $this->mensagem('Agendamento não encontrado.');
            $this->redirecionar('/agendamentos');
        }
        if ($hora === '') {
            $hora = substr($agendamento['hora_agendamento'], 0, 5);
        }
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null) || !Validacao::data($data) ||
            !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora)) {
            $this->mensagem('Informe data e hora válidas e envie o formulário novamente.');
            $this->redirecionar('/agendamentos/editar-data?id=' . $id);
        }
        $ok = Agendamento::atualizarData($id, $data, $hora);
        $this->mensagem($ok ? 'Data e hora atualizadas.' : 'Agendamento não encontrado.');
        $this->redirecionar('/agendamentos');
    }
}
