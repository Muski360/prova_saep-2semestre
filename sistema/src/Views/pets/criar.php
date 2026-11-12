<div class="card form-card"><h2>Novo pet</h2>
<?php if (!empty($erro)): ?><p class="alerta-erro" role="alert"><?= htmlspecialchars($erro) ?></p><?php endif; ?>
<?php if (!$tutores): ?><p>Cadastre um tutor antes de cadastrar um pet.</p><a href="/tutores/novo" class="btn">Novo tutor</a><?php else: ?>
<form action="/pets/salvar" method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
<div class="form-group"><label for="tutor_id">Tutor</label><select name="tutor_id" id="tutor_id" required>
<option value="">Selecione</option>
<?php foreach ($tutores as $t): ?><option value="<?= (int)$t['id'] ?>" <?= (int)($pet['tutor_id'] ?? 0) === (int)$t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nome']) ?></option><?php endforeach; ?>
</select></div>
<?php foreach (['nome' => 'Nome', 'especie' => 'Espécie', 'raca' => 'Raça', 'data_nascimento' => 'Data de nascimento'] as $campo => $rotulo): ?>
<div class="form-group"><label for="<?= $campo ?>"><?= $rotulo ?></label>
<input id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $campo === 'data_nascimento' ? 'date' : 'text' ?>" maxlength="128" value="<?= htmlspecialchars($pet[$campo] ?? '') ?>" required></div>
<?php endforeach; ?>
<button class="btn">Salvar pet</button> <a class="btn btn-secondary" href="/pets">Voltar</a>
</form><?php endif; ?></div>
