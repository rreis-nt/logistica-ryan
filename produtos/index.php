<?php
require_once __DIR__ . '/../includes/functions.php';
$produtos = db()->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
render_header('Produtos', 'produtos');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0"><i class="bi bi-box-seam"></i> Produtos</h1>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo produto</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>ID</th><th>Nome</th><th>Descrição</th><th class="text-end">Peso</th><th class="text-end">Valor</th><th class="text-end">Ações</th></tr></thead>
    <tbody>
    <?php foreach ($produtos as $p): ?>
      <tr>
        <td><?= (int) $p['id'] ?></td>
        <td><?= e($p['nome']) ?></td>
        <td><?= e($p['descricao']) ?></td>
        <td class="text-end"><?= e(kg($p['peso_kg'])) ?></td>
        <td class="text-end"><?= e(money($p['valor'])) ?></td>
        <td class="text-end text-nowrap">
          <a href="form.php?id=<?= (int) $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
          <form action="excluir.php" method="post" class="d-inline" data-confirm="Excluir o produto &quot;<?= e($p['nome']) ?>&quot;?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$produtos): ?><tr><td colspan="6" class="text-center text-muted py-4">Nenhum produto cadastrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>
<?php render_footer(); ?>
