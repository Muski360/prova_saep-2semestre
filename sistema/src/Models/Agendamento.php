<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\ConexaoBanco;
use App\Core\Criptografia;
use PDO;

final class Agendamento
{
    private static function pdo(): PDO
    {
        return ConexaoBanco::obterConexao(dirname(__DIR__, 2) . '/config/database.ini');
    }
    public static function listarPorData(): array
    {
        $sql = "SELECT a.*, p.nome AS pet_nome, p.especie AS pet_especie, p.raca AS pet_raca,
                p.data_nascimento, p.cadastrado_em AS pet_cadastrado_em,
                t.id AS tutor_id, t.nome AS tutor_nome, t.telefone AS tutor_telefone,
                t.email AS tutor_email, t.cpf_criptografado, t.criado_em AS tutor_criado_em,
                u.nome AS responsavel_nome
                FROM agendamentos a JOIN pets p ON p.id = a.pet_id
                JOIN tutores t ON t.id = p.tutor_id JOIN usuarios u ON u.id = a.usuario_id
                ORDER BY a.data_agendamento, a.hora_agendamento, a.id";
        $lista = self::pdo()->query($sql)->fetchAll();
        foreach ($lista as &$a) { $a['tutor_cpf'] = Criptografia::decifrar($a['cpf_criptografado']); }
        return $lista;
    }
    public static function buscarPorId(int $id): ?array
    {
        $stmt = self::pdo()->prepare('SELECT a.*, p.nome AS pet_nome, t.nome AS tutor_nome FROM agendamentos a JOIN pets p ON p.id = a.pet_id JOIN tutores t ON t.id = p.tutor_id WHERE a.id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
    public static function salvar(int $pet, int $usuario, string $data, string $hora, string $servico): bool
    {
        $stmt = self::pdo()->prepare('INSERT INTO agendamentos (pet_id, usuario_id, data_agendamento, hora_agendamento, servico) VALUES (:pet, :usuario, :data, :hora, :servico)');
        return $stmt->execute([':pet' => $pet, ':usuario' => $usuario, ':data' => $data, ':hora' => $hora, ':servico' => $servico]);
    }
    public static function atualizarData(int $id, string $data, string $hora): bool
    {
        $stmt = self::pdo()->prepare('UPDATE agendamentos SET data_agendamento = :data, hora_agendamento = :hora WHERE id = :id');
        $stmt->execute([':id' => $id, ':data' => $data, ':hora' => $hora]);
        return $stmt->rowCount() > 0;
    }
}
