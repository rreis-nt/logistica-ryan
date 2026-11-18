<?php
require_once __DIR__ . '/../includes/functions.php';
$data = (string) ($_GET['data'] ?? date('Y-m-d'));
$valida = valid_date($data);
$fretes = $valida ? fretes_resumo($data) : [];
$totalFrete = array_sum(array_column($fretes, 'valor_frete'));
$totalPeso = array_sum(array_column($fretes, 'peso_total'));
render_header('Relatórios', 'relatorios');
?>
<h1 class="h3 mb-3"><i class="bi bi-file-earmark-arrow-down"></i> Relatório de fretes por dia</h1>
<?php if (!$valida): ?><div class="alert alert-danger">Data inválida.</div><?php endif; ?>
<form method="get" class="row g-2 align-items-end mb-3">
  <div class="col-auto"><label class="form-label mb-0" for="data">Data</label>
    <input type="date" class="form-control" id="data" name="data" value="<?= e($data) ?>" required></div>
  <div class="col-auto"><button class="btn btn-outline-primary"><i class="bi bi-search"></i> Consultar</button></div>
</form>
<?php if ($valida): ?>
<div class="card mb-3"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
  <div><strong><?= count($fretes) ?></strong> frete(s) em <strong><?= e(fmt_date($data)) ?></strong> &middot; peso total <strong><?= e(kg($totalPeso)) ?></strong> &middot; soma dos fretes <strong><?= e(money($totalFrete)) ?></strong></div>
  <div class="d-flex gap-2 <?= $fretes ? '' : 'disabled' ?>">
    <?php if ($fretes): ?>
      <a class="btn btn-danger" href="download.php?data=<?= e($data) ?>&formato=pdf"><i class="bi bi-file-earmark-pdf"></i> Baixar PDF</a>
      <a class="btn btn-success" href="download.php?data=<?= e($data) ?>&formato=csv"><i class="bi bi-file-earmark-spreadsheet"></i> Baixar CSV</a>
    <?php endif; ?>
  </div>
</div></div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead class="table-light"><tr><th>Nº</th><th>Transportador</th><th>Rota</th><th class="text-end">Itens</th><th class="text-end">Peso</th><th class="text-end">Valor do frete</th></tr></thead>
  <tbody>
  <?php foreach ($fretes as $f): ?>
    <tr><td><a href="<?= e(url('fretes/ver.php?id=' . (int) $f['id'])) ?>"><?= (int) $f['id'] ?></a></td><td><?= e($f['transportador']) ?></td>
      <td><?= e($f['origem']) ?> <i class="bi bi-arrow-right"></i> <?= e($f['destino']) ?></td>
      <td class="text-end"><?= (int) $f['qtd_itens'] ?></td><td class="text-end"><?= e(kg($f['peso_total'])) ?></td><td class="text-end"><?= e(money($f['valor_frete'])) ?></td></tr>
  <?php endforeach; ?>
  <?php if (!$fretes): ?><tr><td colspan="6" class="text-center text-muted py-4">Nenhum frete nesta data.</td></tr><?php endif; ?>
  </tbody>
</table></div></div>
<?php endif; ?>
<?php render_footer(); ?>
