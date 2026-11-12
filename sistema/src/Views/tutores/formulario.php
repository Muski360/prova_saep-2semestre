<div class="card form-card">
<h2><?= htmlspecialchars($titulo) ?></h2>
<?php if (!empty($erro)): ?><p class="alerta-erro" role="alert"><?= htmlspecialchars($erro) ?></p><?php endif; ?>
<form method="post" action="<?= !empty($tutor['id']) ? '/tutores/atualizar' : '/tutores/salvar' ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="id" value="<?= (int)($tutor['id'] ?? 0) ?>">
    <?php foreach (['nome' => 'Nome', 'cpf' => 'CPF', 'telefone' => 'Telefone com DDD', 'email' => 'E-mail'] as $campo => $rotulo): ?>
    <div class="form-group">
        <label for="<?= $campo ?>"><?= $rotulo ?></label>
        <input id="<?= $campo ?>" name="<?= $campo ?>" type="<?= $campo === 'email' ? 'email' : 'text' ?>" maxlength="128" value="<?= htmlspecialchars($tutor[$campo] ?? '') ?>" required>
    </div>
    <?php endforeach; ?>
    <button class="btn">Salvar tutor</button> <a class="btn btn-secondary" href="/tutores">Voltar</a>
</form>
</div>
