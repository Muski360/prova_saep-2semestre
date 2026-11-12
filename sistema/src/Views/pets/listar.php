<div class="card"><h2>Pets</h2><a class="btn" href="/pets/novo">Novo pet</a>
<div class="table-scroll"><table><thead><tr><th>Nome</th><th>Espécie</th><th>Raça</th><th>Nascimento</th><th>Tutor</th></tr></thead><tbody>
<?php foreach ($pets as $p): ?><tr>
<td><?= htmlspecialchars($p['nome']) ?></td><td><?= htmlspecialchars($p['especie']) ?></td><td><?= htmlspecialchars($p['raca']) ?></td>
<td><?= htmlspecialchars(date('d/m/Y', strtotime($p['data_nascimento']))) ?></td><td><?= htmlspecialchars($p['tutor_nome']) ?></td>
</tr><?php endforeach; ?>
<?php if (!$pets): ?><tr><td colspan="5">Nenhum pet cadastrado.</td></tr><?php endif; ?>
</tbody></table></div></div>
