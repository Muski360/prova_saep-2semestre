<div class="card form-card">
    <h2>Alterar data e hora</h2>
    <p class="muted"><?= htmlspecialchars($agendamento['pet_nome']) ?> · Tutor: <?= htmlspecialchars($agendamento['tutor_nome']) ?></p>
    <form action="/agendamentos/atualizar-data" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="id" value="<?= (int)$agendamento['id'] ?>">
        <div class="form-group">
            <label for="data_agendamento">Data</label>
            <input type="date" id="data_agendamento" name="data_agendamento" value="<?= htmlspecialchars($agendamento['data_agendamento']) ?>" required>
        </div>
        <div class="form-group">
            <label for="hora_agendamento">Horário</label>
            <input type="time" id="hora_agendamento" name="hora_agendamento" value="<?= htmlspecialchars(substr($agendamento['hora_agendamento'], 0, 5)) ?>">
        </div>
        <div class="form-actions">
            <button class="btn">Salvar alterações</button>
            <a href="/agendamentos" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
