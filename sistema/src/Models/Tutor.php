<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\ConexaoBanco;
use App\Core\Criptografia;
use PDO;

/**
 * Modelo de negocio para gestao de tutores com suporte a LGPD e busca.
 */
final class Tutor
{
    private static function pdo(): PDO
    {
        $cfg = dirname(__DIR__, 2) . '/config/database.ini';
        return ConexaoBanco::obterConexao($cfg);
    }

    public static function listar(?string $termo = null): array
    {
        if (!empty($termo)) {
            $sql = "SELECT id, nome, cpf_criptografado, telefone, email, criado_em 
                    FROM tutores 
                    WHERE nome ILIKE :nome OR email ILIKE :email OR telefone ILIKE :telefone 
                    ORDER BY nome ASC";
            $stmt = self::pdo()->prepare($sql);
            $stmt->execute([':nome' => '%' . trim($termo) . '%', ':email' => '%' . trim($termo) . '%', ':telefone' => '%' . trim($termo) . '%']);
            $registros = $stmt->fetchAll();
        } else {
            $sql = "SELECT id, nome, cpf_criptografado, telefone, email, criado_em FROM tutores ORDER BY nome ASC";
            $registros = self::pdo()->query($sql)->fetchAll();
        }
        return self::descriptografarColecao($registros);
    }

    public static function buscarPorId(int $id): ?array
    {
        $sql = "SELECT id, nome, cpf_criptografado, telefone, email FROM tutores WHERE id = :id LIMIT 1";
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $tutor = $stmt->fetch();
        if ($tutor) {
            $tutor['cpf'] = Criptografia::decifrar($tutor['cpf_criptografado']);
        }
        return $tutor ?: null;
    }

    public static function salvar(string $nome, string $cpf, string $telefone, string $email): bool
    {
        $sql = "INSERT INTO tutores (nome, cpf_criptografado, telefone, email) 
                VALUES (:nome, :cpf, :telefone, :email)";
        $stmt = self::pdo()->prepare($sql);
        return $stmt->execute([
            ':nome'     => trim($nome),
            ':cpf'      => Criptografia::cifrar(trim($cpf)),
            ':telefone' => trim($telefone),
            ':email'    => trim($email)
        ]);
    }

    public static function atualizar(int $id, string $nome, string $cpf, string $tel, string $email): bool
    {
        $sql = "UPDATE tutores 
                SET nome = :nome, cpf_criptografado = :cpf, telefone = :tel, email = :email 
                WHERE id = :id";
        $stmt = self::pdo()->prepare($sql);
        return $stmt->execute([
            ':nome'  => trim($nome),
            ':cpf'   => Criptografia::cifrar(trim($cpf)),
            ':tel'   => trim($tel),
            ':email' => trim($email),
            ':id'    => $id
        ]);
    }

    public static function excluir(int $id): bool
    {
        $stmt = self::pdo()->prepare('DELETE FROM tutores WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    private static function descriptografarColecao(array $itens): array
    {
        foreach ($itens as &$item) {
            $item['cpf'] = Criptografia::decifrar($item['cpf_criptografado']);
        }
        return $itens;
    }
}
