<div class="card form-card"><h2>Novo agendamento</h2>
<?php if (!empty($erro)): ?><p class="alerta-erro" role="alert"><?= htmlspecialchars($erro) ?></p><?php endif; ?>
<?php if (!$pets): ?><p>Cadastre um pet antes de agendar.</p><a class="btn" href="/pets/novo">Novo pet</a><?php else: ?>
<form method="post" action="/agendamentos/salvar">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
<div class="form-group"><label for="pet_id">Pet e tutor responsável</label><select id="pet_id" name="pet_id" required><option value="">Selecione</option>
<?php foreach ($pets as $p): ?><option value="<?= (int)$p['id'] ?>" <?= (int)($dados['pet_id'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nome'] . ' — Tutor: ' . $p['tutor_nome']) ?></option><?php endforeach; ?>
</select></div>
<?php foreach (['data_agendamento' => ['Data', 'date'], 'hora_agendamento' => ['Hora', 'time'], 'servico' => ['Serviço', 'text']] as $campo => $info): ?>
<div class="form-group"><label for="<?= $campo ?>"><?= $info[0] ?></label><input id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $info[1] ?>" value="<?= htmlspecialchars($dados[$campo] ?? '') ?>" maxlength="128" required></div>
<?php endforeach; ?>
<button class="btn">Salvar agendamento</button> <a class="btn btn-secondary" href="/agendamentos">Voltar</a></form><?php endif; ?></div>
