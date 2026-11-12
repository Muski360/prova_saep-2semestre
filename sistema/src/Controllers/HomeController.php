<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Tutor;
use App\Models\Pet;
use App\Models\Agendamento;

/**
 * Controller da tela principal do sistema com menu de recursos e metricas.
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $usuarioNome = $_SESSION['usuario_nome'] ?? 'Operador';
        $totalTutores = count(Tutor::listar());
        $totalPets = Pet::totalPets();
        $agendamentosRecentes = array_slice(array_values(array_filter(Agendamento::listarPorData(), fn($a) => $a['status'] === 'AGENDADO' && $a['data_agendamento'] . ' ' . $a['hora_agendamento'] >= date('Y-m-d H:i:s'))), 0, 5);

        $this->renderizar('home/index', [
            'titulo'               => 'Painel Principal - Clínica Veterinária',
            'usuarioNome'          => $usuarioNome,
            'totalTutores'         => $totalTutores,
            'totalPets'            => $totalPets,
            'agendamentosRecentes' => $agendamentosRecentes
        ]);
    }
}
