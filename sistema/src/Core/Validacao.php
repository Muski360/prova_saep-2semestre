<?php
declare(strict_types=1);
namespace App\Core;

final class Validacao
{
    public static function data(string $data): bool
    {
        $dt = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);
        return $dt !== false && $dt->format('Y-m-d') === $data;
    }

    public static function cpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int)$cpf[$i] * ($t + 1 - $i);
            }
            $digito = (10 * $soma) % 11;
            if (($digito === 10 ? 0 : $digito) !== (int)$cpf[$t]) {
                return false;
            }
        }
        return true;
    }
}
