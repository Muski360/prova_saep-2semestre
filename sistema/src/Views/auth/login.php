<div class="card login-card">
    <h2>Entrar</h2>
    <p class="muted">Acesse com seu e-mail e senha.</p>
    <?php if (!empty($erro)): ?>
        <p class="alerta-erro" role="alert"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <form action="/login" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" autocomplete="username" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" autocomplete="current-password" required>
        </div>
        <button class="btn btn-block">Entrar</button>
    </form>
    <p class="login-note">A sessão encerra automaticamente após um período sem uso.</p>
</div>
