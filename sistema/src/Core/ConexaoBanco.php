<?php

declare(strict_types=1);

namespace App\Core;

// Escrever uma Provedor de Conexão usando Singleton

use PDO;
use Exception;

final class ConexaoBanco
{
    private static ?PDO $instancia = null;

    private function __construct() {}
    private function __clone(): void {}

    public function __wakeup(): void
    {
        throw new Exception("Desserializacao nao permitida.");
    }

    public static function obterConexao(string $caminhoConfig): PDO
    {
        if (self::$instancia === null) {
            $dados = parse_ini_file($caminhoConfig, true);
            $cfg = $dados['database'];
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s",
                $cfg['db_driver'],
                $cfg['db_host'],
                $cfg['db_port'],
                $cfg['db_name']
            );
            self::$instancia = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]);
        }
        return self::$instancia;
    }
}