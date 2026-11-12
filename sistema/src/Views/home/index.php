<div class="card">
    <h2>Painel</h2>
    <p class="muted">Olá, <?= htmlspecialchars($usuarioNome) ?>.</p>
    <div class="quick-links">
        <a href="/tutores">Tutores<span><?= (int)$totalTutores ?> cadastrados</span></a>
        <a href="/pets">Pets<span><?= (int)$totalPets ?> cadastrados</span></a>
        <a href="/agendamentos">Agendamentos<span>Consultar agenda</span></a>
    </div>
    <h3 class="section-title">Próximos atendimentos</h3>
    <div class="table-scroll">
    <table>
        <thead><tr><th>Data</th><th>Horário</th><th>Pet</th><th>Tutor</th><th>Serviço</th></tr></thead>
        <tbody>
        <?php foreach ($agendamentosRecentes as $a): ?>
            <tr>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($a['data_agendamento']))) ?></td>
                <td><?= htmlspecialchars(substr($a['hora_agendamento'], 0, 5)) ?></td>
                <td><?= htmlspecialchars($a['pet_nome']) ?></td>
                <td><?= htmlspecialchars($a['tutor_nome']) ?></td>
                <td><?= htmlspecialchars($a['servico']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$agendamentosRecentes): ?><tr><td colspan="5" class="empty">Nenhum atendimento previsto.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
