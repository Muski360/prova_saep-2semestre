<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Pet;
use App\Models\Tutor;
use App\Core\Validacao;

final class PetController extends Controller
{
    public function listar(): void
    {
        $this->renderizar('pets/listar', ['titulo' => 'Pets', 'pets' => Pet::listarTodos()]);
    }
    public function formulario(): void
    {
        $this->renderizar('pets/criar', ['titulo' => 'Novo pet', 'tutores' => Tutor::listar()]);
    }
    public function salvar(): void
    {
        $tutor = (int)($_POST['tutor_id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $especie = trim($_POST['especie'] ?? '');
        $raca = trim($_POST['raca'] ?? '');
        $data = trim($_POST['data_nascimento'] ?? '');
        if (!$this->validarCsrfToken($_POST['csrf_token'] ?? null) || !Tutor::buscarPorId($tutor) ||
            $nome === '' || $especie === '' || $raca === '' || mb_strlen($nome) > 128 || mb_strlen($especie) > 128 || mb_strlen($raca) > 128 ||
            !Validacao::data($data) || $data > date('Y-m-d')) {
            $this->renderizar('pets/criar', ['titulo' => 'Novo pet', 'tutores' => Tutor::listar(), 'erro' => 'Selecione um tutor existente, preencha os campos (até 128 caracteres) e informe nascimento válido, até hoje.', 'pet' => $_POST]);
            return;
        }
        Pet::salvar($tutor, $nome, $especie, $raca, $data);
        $this->mensagem('Pet cadastrado.');
        $this->redirecionar('/pets');
    }
}
