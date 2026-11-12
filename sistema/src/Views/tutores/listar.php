<div class="card">
<h2>Tutores</h2>
<a class="btn" href="/tutores/novo">Novo tutor</a>
<form method="get" action="/tutores" class="search-form">
    <label for="q">Buscar por nome, e-mail ou telefone</label>
    <input id="q" name="q" value="<?= htmlspecialchars($busca) ?>">
    <button class="btn">Buscar</button> <a class="btn btn-secondary" href="/tutores">Limpar</a>
</form>
<div class="table-scroll"><table>
<thead><tr><th>Nome</th><th>CPF</th><th>Telefone</th><th>E-mail</th><th>Ações</th></tr></thead>
<tbody>
<?php foreach ($tutores as $t): ?>
<tr>
<td><?= htmlspecialchars($t['nome']) ?></td><td><?= htmlspecialchars($t['cpf']) ?></td>
<td><?= htmlspecialchars($t['telefone']) ?></td><td><?= htmlspecialchars($t['email']) ?></td>
<td><a class="btn" href="/tutores/editar?id=<?= (int)$t['id'] ?>">Editar</a>
<form action="/tutores/excluir" method="post" class="inline-form" onsubmit="return confirm('Excluir este tutor?')">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
<input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
<button class="btn btn-danger">Excluir</button></form></td>
</tr>
<?php endforeach; ?>
<?php if (!$tutores): ?><tr><td colspan="5">Nenhum tutor encontrado.</td></tr><?php endif; ?>
</tbody></table></div></div>
