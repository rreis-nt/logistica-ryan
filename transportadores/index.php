<?php
require_once __DIR__ . '/../includes/functions.php';
$lista = db()->query('SELECT * FROM transportadores ORDER BY nome')->fetchAll();
render_header('Transportadores', 'transportadores');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0"><i class="bi bi-truck"></i> Transportadores</h1>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo transportador</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>ID</th><th>Nome</th><th>CPF/CNPJ</th><th>Contato</th><th>Veículo</th><th class="text-end">Ações</th></tr></thead>
    <tbody>
    <?php foreach ($lista as $t): ?>
      <tr>
        <td><?= (int) $t['id'] ?></td>
        <td><?= e($t['nome']) ?></td>
        <td><?= e(fmt_doc($t['documento'])) ?></td>
        <td><?= e($t['telefone']) ?><?= $t['email'] ? '<br><small class="text-muted">' . e($t['email']) . '</small>' : '' ?></td>
        <td><?= e(trim($t['veiculo'] . ' ' . $t['placa'])) ?></td>
        <td class="text-end text-nowrap">
          <a href="form.php?id=<?= (int) $t['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
          <form action="excluir.php" method="post" class="d-inline" data-confirm="Excluir o transportador &quot;<?= e($t['nome']) ?>&quot;?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$lista): ?><tr><td colspan="6" class="text-center text-muted py-4">Nenhum transportador cadastrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>
<?php render_footer(); ?>
