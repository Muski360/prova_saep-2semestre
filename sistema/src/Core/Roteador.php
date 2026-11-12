<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Roteador HTTP para despacho de controladores com suporte a Guards.
 */
final class Roteador
{
    private array $rotas = [];

    public function registrar(string $metodo, string $uri, array $acao, array $guards = []): void
    {
        $uriNormalizada = '/' . trim($uri, '/');
        $this->rotas[strtoupper($metodo)][$uriNormalizada] = [
            'acao'   => $acao,
            'guards' => $guards
        ];
    }

    public function despachar(string $metodo, string $uri): void
    {
        $uriLimpa = '/' . trim((string)parse_url($uri, PHP_URL_PATH), '/');
        $metodoHttp = strtoupper($metodo);
        if (isset($this->rotas[$metodoHttp][$uriLimpa])) {
            $registro = $this->rotas[$metodoHttp][$uriLimpa];
            foreach ($registro['guards'] as $guard) {
                [$classeGuard, $metodoGuard] = $guard;
                $classeGuard::$metodoGuard();
            }
            [$classeController, $nomeMetodo] = $registro['acao'];
            (new $classeController())->$nomeMetodo();
            return;
        }
        http_response_code(404);
        echo "<h2 style='font-family:sans-serif;text-align:center;'>404 - Rota Nao Encontrada</h2>";
    }
}
