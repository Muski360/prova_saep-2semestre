<?php
declare(strict_types=1);
namespace App\Core;

final class Criptografia
{
    private static function chave(): string
    {
        $cfg = parse_ini_file(dirname(__DIR__, 2) . '/config/database.ini', true);
        return hash('sha256', $cfg['security']['app_key'], true);
    }

    public static function cifrar(string $cpf): string
    {
        $iv = random_bytes(12);
        $cifra = openssl_encrypt($cpf, 'aes-256-gcm', self::chave(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($cifra === false) {
            throw new \RuntimeException('Não foi possível proteger o CPF.');
        }
        return base64_encode($iv . $tag . $cifra);
    }

    public static function decifrar(string $valor): string
    {
        $dados = base64_decode($valor, true);
        if ($dados === false || strlen($dados) < 29) {
            return 'CPF indisponível';
        }
        $cpf = openssl_decrypt(substr($dados, 28), 'aes-256-gcm', self::chave(), OPENSSL_RAW_DATA,
            substr($dados, 0, 12), substr($dados, 12, 16));
        return $cpf === false ? 'CPF indisponível' : $cpf;
    }
}
