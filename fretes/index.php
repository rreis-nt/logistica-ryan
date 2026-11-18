<?php
require_once __DIR__ . '/../includes/functions.php';
$data = (string) ($_GET['data'] ?? '');
$filtro = valid_date($data) ? $data : null;
$fretes = fretes_resumo($filtro);
render_header('Fretes', 'fretes');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0"><i class="bi bi-clipboard-check"></i> Fretes</h1>
  <a href="form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Gerar novo frete</a>
</div>
<form method="get" class="row g-2 align-items-end mb-3">
  <div class="col-auto"><label class="form-label mb-0" for="data">Filtrar por data</label>
    <input type="date" class="form-control" id="data" name="data" value="<?= e($filtro ?? '') ?>"></div>
  <div class="col-auto"><button class="btn btn-outline-secondary"><i class="bi bi-funnel"></i> Filtrar</button>
    <?php if ($filtro): ?><a href="index.php" class="btn btn-link">Limpar</a><?php endif; ?></div>
</form>
<div class="card"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>Nº</th><th>Data</th><th>Transportador</th><th>Rota</th><th class="text-end">Itens</th><th class="text-end">Peso</th><th class="text-end">Valor do frete</th><th class="text-end">Ações</th></tr></thead>
    <tbody>
    <?php foreach ($fretes as $f): ?>
      <tr>
        <td><?= (int) $f['id'] ?></td>
        <td><?= e(fmt_date($f['data_frete'])) ?></td>
        <td><?= e($f['transportador']) ?></td>
        <td><?= e($f['origem']) ?> <i class="bi bi-arrow-right"></i> <?= e($f['destino']) ?></td>
        <td class="text-end"><?= (int) $f['qtd_itens'] ?></td>
        <td class="text-end"><?= e(kg($f['peso_total'])) ?></td>
        <td class="text-end"><?= e(money($f['valor_frete'])) ?></td>
        <td class="text-end text-nowrap">
          <a href="ver.php?id=<?= (int) $f['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> Ver</a>
          <a href="form.php?id=<?= (int) $f['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
          <form action="excluir.php" method="post" class="d-inline" data-confirm="Excluir o frete nº <?= (int) $f['id'] ?>?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$fretes): ?><tr><td colspan="8" class="text-center text-muted py-4">Nenhum frete encontrado.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div></div>
<?php render_footer(); ?>
