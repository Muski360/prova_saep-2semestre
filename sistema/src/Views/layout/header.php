<?php
$usuarioLogado = $_SESSION['usuario_nome'] ?? null;
$rota = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'SupVet') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?= $usuarioLogado ? '/' : '/login' ?>">SupVet<small>Gestão veterinária</small></a>
        <?php if ($usuarioLogado): ?>
        <nav aria-label="Menu principal">
            <?php foreach (['/' => 'Painel', '/tutores' => 'Tutores', '/pets' => 'Pets', '/agendamentos' => 'Agendamentos'] as $url => $nome): ?>
                <a href="<?= $url ?>" <?= ($url === '/' ? $rota === '/' : str_starts_with($rota, $url)) ? 'aria-current="page"' : '' ?>><?= $nome ?></a>
            <?php endforeach; ?>
            <span class="user-name"><?= htmlspecialchars($usuarioLogado) ?></span>
            <form action="/logout" method="post" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button class="btn btn-secondary">Sair</button>
            </form>
        </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container">
    <?php if (!empty($_SESSION['mensagem'])): ?>
        <p class="mensagem" role="status"><?= htmlspecialchars($_SESSION['mensagem']) ?></p>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>
