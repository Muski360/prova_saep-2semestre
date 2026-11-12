<div class="card"><h2>Agendamentos por data</h2>
<p>Ordenados por dia e horário, do mais antigo para o mais recente.</p>
<a class="btn" href="/agendamentos/novo">Novo agendamento</a>
<div class="table-scroll"><table><thead><tr><th>Data / hora</th><th>Pet</th><th>Tutor</th><th>Serviço</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($agendamentos as $a): ?><tr>
<td><?= htmlspecialchars(date('d/m/Y', strtotime($a['data_agendamento'])) . ' ' . substr($a['hora_agendamento'], 0, 5)) ?></td>
<td><strong><?= htmlspecialchars($a['pet_nome']) ?></strong>
<details><summary>Dados do pet</summary>
ID: <?= (int)$a['pet_id'] ?><br>Espécie: <?= htmlspecialchars($a['pet_especie']) ?><br>Raça: <?= htmlspecialchars($a['pet_raca']) ?><br>
Nascimento: <?= htmlspecialchars(date('d/m/Y', strtotime($a['data_nascimento']))) ?><br>
Cadastro: <?= htmlspecialchars($a['pet_cadastrado_em']) ?></details></td>
<td><?= htmlspecialchars($a['tutor_nome']) ?><details><summary>Dados do tutor</summary>
ID: <?= (int)$a['tutor_id'] ?><br>CPF: <?= htmlspecialchars($a['tutor_cpf']) ?><br>Telefone: <?= htmlspecialchars($a['tutor_telefone']) ?><br>
E-mail: <?= htmlspecialchars($a['tutor_email']) ?><br>Cadastro: <?= htmlspecialchars($a['tutor_criado_em']) ?></details></td>
<td><?= htmlspecialchars($a['servico']) ?><br><?= htmlspecialchars($a['status']) ?><br>
Responsável: <?= htmlspecialchars($a['responsavel_nome']) ?><br><?= htmlspecialchars($a['observacoes'] ?? '') ?></td>
<td><a class="btn" href="/agendamentos/editar-data?id=<?= (int)$a['id'] ?>">Alterar data</a></td>
</tr><?php endforeach; ?>
<?php if (!$agendamentos): ?><tr><td colspan="5">Nenhum agendamento.</td></tr><?php endif; ?>
</tbody></table></div></div>
