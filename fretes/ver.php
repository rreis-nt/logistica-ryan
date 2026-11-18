<?php
require_once __DIR__ . '/../includes/functions.php';
$id = (int) ($_GET['id'] ?? 0);
$res = fretes_resumo(null, $id);
if (!$res) { flash('danger', 'Frete não encontrado.'); redirect('fretes/index.php'); }
$f = $res[0];
$itens = frete_itens($id);

$st = db()->prepare('SELECT * FROM enderecos WHERE id = ?');
$st->execute([$f['origem_id']]);  $origem = $st->fetch();
$st->execute([$f['destino_id']]); $destino = $st->fetch();
$st = db()->prepare('SELECT * FROM transportadores WHERE id = ?');
$st->execute([$f['transportador_id']]); $transp = $st->fetch();

render_header('Frete nº ' . $id, 'fretes');
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0"><i class="bi bi-clipboard-check"></i> Frete nº <?= $id ?></h1>
  <div class="d-flex gap-2">
    <a href="form.php?id=<?= $id ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
    <a href="index.php" class="btn btn-outline-secondary">Voltar</a>
  </div>
</div>
<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="card h-100"><div class="card-body">
    <h2 class="h6 text-muted">Transportador</h2>
    <p class="mb-1 fw-semibold"><?= e($transp['nome']) ?></p>
    <p class="mb-0 small"><?= e(fmt_doc($transp['documento'])) ?><br><?= e(trim(($transp['veiculo'] ?? '') . ' ' . ($transp['placa'] ?? ''))) ?><br><?= e($transp['telefone'] ?? '') ?></p>
  </div></div></div>
  <div class="col-md-4"><div class="card h-100"><div class="card-body">
    <h2 class="h6 text-muted">Origem</h2>
    <p class="mb-1 fw-semibold"><?= e($origem['apelido']) ?></p>
    <p class="mb-0 small"><?= e(fmt_endereco($origem)) ?></p>
  </div></div></div>
  <div class="col-md-4"><div class="card h-100"><div class="card-body">
    <h2 class="h6 text-muted">Destino</h2>
    <p class="mb-1 fw-semibold"><?= e($destino['apelido']) ?></p>
    <p class="mb-0 small"><?= e(fmt_endereco($destino)) ?></p>
  </div></div></div>
</div>
<div class="card mb-3"><div class="card-header bg-white fw-semibold">Produtos transportados</div>
<div class="table-responsive"><table class="table mb-0 align-middle">
  <thead class="table-light"><tr><th>Produto</th><th class="text-end">Qtd.</th><th class="text-end">Peso unit.</th><th class="text-end">Valor unit.</th><th class="text-end">Peso total</th><th class="text-end">Subtotal</th></tr></thead>
  <tbody>
  <?php foreach ($itens as $i): ?>
    <tr><td><?= e($i['nome']) ?></td><td class="text-end"><?= (int) $i['quantidade'] ?></td>
      <td class="text-end"><?= e(kg($i['peso_kg'])) ?></td><td class="text-end"><?= e(money($i['valor'])) ?></td>
      <td class="text-end"><?= e(kg($i['peso_kg'] * $i['quantidade'])) ?></td><td class="text-end"><?= e(money($i['valor'] * $i['quantidade'])) ?></td></tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot class="table-light"><tr><th>Total</th><th class="text-end"><?= (int) $f['qtd_itens'] ?></th><th></th><th></th>
    <th class="text-end"><?= e(kg($f['peso_total'])) ?></th><th class="text-end"><?= e(money($f['valor_carga'])) ?></th></tr></tfoot>
</table></div></div>
<div class="row g-3">
  <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Data</div><div class="fs-5"><?= e(fmt_date($f['data_frete'])) ?></div></div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Valor do frete</div><div class="fs-5"><?= e(money($f['valor_frete'])) ?></div></div></div></div>
  <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Observações</div><div><?= e($f['observacoes'] ?: '—') ?></div></div></div></div>
</div>
<?php render_footer(); ?>
