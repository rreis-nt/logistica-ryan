<?php
require_once __DIR__ . '/../includes/functions.php';
$lista = db()->query('SELECT * FROM enderecos ORDER BY apelido')->fetchAll();
render_header('Endereços', 'enderecos');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 mb-0"><i class="bi bi-geo-alt"></i> Endereços (origem e destino)</h1>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo endereço</a>
</div>
<div class="card"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>ID</th><th>Identificação</th><th>Endereço</th><th class="text-end">Ações</th></tr></thead>
    <tbody>
    <?php foreach ($lista as $a): ?>
      <tr>
        <td><?= (int) $a['id'] ?></td>
        <td><?= e($a['apelido']) ?></td>
        <td><?= e(fmt_endereco($a)) ?></td>
        <td class="text-end text-nowrap">
          <a href="form.php?id=<?= (int) $a['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
          <form action="excluir.php" method="post" class="d-inline" data-confirm="Excluir o endereço &quot;<?= e($a['apelido']) ?>&quot;?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$lista): ?><tr><td colspan="4" class="text-center text-muted py-4">Nenhum endereço cadastrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>
<?php render_footer(); ?>
