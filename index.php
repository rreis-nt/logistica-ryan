<?php
require_once __DIR__ . '/includes/functions.php';
$pdo = db();
$cont = [
    'Produtos' => [(int) $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn(), 'produtos/index.php', 'bi-box-seam'],
    'Endereços' => [(int) $pdo->query('SELECT COUNT(*) FROM enderecos')->fetchColumn(), 'enderecos/index.php', 'bi-geo-alt'],
    'Transportadores' => [(int) $pdo->query('SELECT COUNT(*) FROM transportadores')->fetchColumn(), 'transportadores/index.php', 'bi-truck'],
    'Fretes' => [(int) $pdo->query('SELECT COUNT(*) FROM fretes')->fetchColumn(), 'fretes/index.php', 'bi-clipboard-check'],
];
$ultimos = array_slice(fretes_resumo(), 0, 5);
render_header('Início', 'inicio');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
  <div><h1 class="h3 mb-0">Painel</h1><p class="text-muted mb-0">Cadastre produtos, endereços e transportadores e gere seus fretes.</p></div>
  <a href="<?= e(url('fretes/form.php')) ?>" class="btn btn-primary btn-lg"><i class="bi bi-plus-lg"></i> Gerar frete</a>
</div>
<div class="row g-3 mb-4">
  <?php foreach ($cont as $nome => [$qtd, $link, $icone]): ?>
    <div class="col-6 col-lg-3"><a href="<?= e(url($link)) ?>" class="text-decoration-none">
      <div class="card card-stat h-100"><div class="card-body">
        <div class="text-muted"><i class="bi <?= e($icone) ?>"></i> <?= e($nome) ?></div>
        <div class="display-6 text-dark"><?= $qtd ?></div>
      </div></div></a></div>
  <?php endforeach; ?>
</div>
<div class="card"><div class="card-header bg-white fw-semibold">Últimos fretes</div>
<div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead class="table-light"><tr><th>Nº</th><th>Data</th><th>Transportador</th><th>Rota</th><th class="text-end">Valor do frete</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($ultimos as $f): ?>
    <tr><td><?= (int) $f['id'] ?></td><td><?= e(fmt_date($f['data_frete'])) ?></td><td><?= e($f['transportador']) ?></td>
      <td><?= e($f['origem']) ?> <i class="bi bi-arrow-right"></i> <?= e($f['destino']) ?></td>
      <td class="text-end"><?= e(money($f['valor_frete'])) ?></td>
      <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('fretes/ver.php?id=' . (int) $f['id'])) ?>">Ver</a></td></tr>
  <?php endforeach; ?>
  <?php if (!$ultimos): ?><tr><td colspan="6" class="text-center text-muted py-4">Nenhum frete gerado ainda.</td></tr><?php endif; ?>
  </tbody>
</table></div></div>
<?php render_footer(); ?>
