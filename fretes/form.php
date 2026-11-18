<?php
require_once __DIR__ . '/../includes/functions.php';

$pdo = db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$transportadores = $pdo->query('SELECT id, nome, veiculo, placa FROM transportadores ORDER BY nome')->fetchAll();
$enderecos = $pdo->query('SELECT id, apelido, cidade, uf FROM enderecos ORDER BY apelido')->fetchAll();
$produtos = $pdo->query('SELECT id, nome, peso_kg, valor FROM produtos ORDER BY nome')->fetchAll();

$dados = ['transportador_id' => '', 'origem_id' => '', 'destino_id' => '', 'data_frete' => date('Y-m-d'), 'valor_frete' => '', 'observacoes' => ''];
$itens = [['produto_id' => '', 'quantidade' => 1]];

if ($id > 0) {
    $st = $pdo->prepare('SELECT * FROM fretes WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('danger', 'Frete não encontrado.'); redirect('fretes/index.php'); }
    $dados = $row;
    $itens = array_map(fn($i) => ['produto_id' => $i['produto_id'], 'quantidade' => $i['quantidade']], frete_itens($id));
}

$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['transportador_id', 'origem_id', 'destino_id', 'data_frete', 'valor_frete', 'observacoes'] as $c) $dados[$c] = post($c);

    // Itens enviados pelo formulário dinâmico (somam quantidades de produtos repetidos)
    $prods = (array) ($_POST['item_produto'] ?? []);
    $qtds = (array) ($_POST['item_qtd'] ?? []);
    $mapa = [];
    foreach ($prods as $k => $pid) {
        $pid = (int) $pid;
        $q = (int) ($qtds[$k] ?? 0);
        if ($pid <= 0) continue;
        if ($q < 1) { $erros[] = 'A quantidade de cada produto deve ser pelo menos 1.'; break; }
        $mapa[$pid] = ($mapa[$pid] ?? 0) + $q;
    }
    $itens = [];
    foreach ($mapa as $pid => $q) $itens[] = ['produto_id' => $pid, 'quantidade' => $q];
    if (!$itens) {
        if (!$erros) $erros[] = 'Adicione pelo menos um produto ao frete.';
        $itens = [['produto_id' => '', 'quantidade' => 1]];
    }

    $idsT = array_column($transportadores, 'id');
    $idsE = array_column($enderecos, 'id');
    if (!in_array((int) $dados['transportador_id'], array_map('intval', $idsT), true)) $erros[] = 'Selecione o transportador.';
    if (!in_array((int) $dados['origem_id'], array_map('intval', $idsE), true)) $erros[] = 'Selecione o endereço de origem.';
    if (!in_array((int) $dados['destino_id'], array_map('intval', $idsE), true)) $erros[] = 'Selecione o endereço de destino.';
    if ($dados['origem_id'] !== '' && $dados['origem_id'] === $dados['destino_id']) $erros[] = 'Origem e destino devem ser diferentes.';
    if (!valid_date($dados['data_frete'])) $erros[] = 'Informe uma data válida.';
    if (!is_numeric(str_replace(',', '.', $dados['valor_frete'])) || to_float($dados['valor_frete']) < 0) $erros[] = 'Informe o valor do frete (zero ou maior).';
    if (mb_strlen($dados['observacoes']) > 255) $erros[] = 'Observações: máximo de 255 caracteres.';

    $idsP = array_map('intval', array_column($produtos, 'id'));
    foreach ($itens as $it) {
        if ($it['produto_id'] !== '' && !in_array((int) $it['produto_id'], $idsP, true)) { $erros[] = 'Produto inválido no frete.'; break; }
    }

    if (!$erros) {
        try {
            $pdo->beginTransaction();
            $p = [(int) $dados['transportador_id'], (int) $dados['origem_id'], (int) $dados['destino_id'], $dados['data_frete'], to_float($dados['valor_frete']), $dados['observacoes'] ?: null];
            if ($id > 0) {
                $pdo->prepare('UPDATE fretes SET transportador_id=?, origem_id=?, destino_id=?, data_frete=?, valor_frete=?, observacoes=? WHERE id=?')->execute([...$p, $id]);
                $pdo->prepare('DELETE FROM frete_itens WHERE frete_id = ?')->execute([$id]);
                $freteId = $id;
            } else {
                $pdo->prepare('INSERT INTO fretes (transportador_id, origem_id, destino_id, data_frete, valor_frete, observacoes) VALUES (?,?,?,?,?,?)')->execute($p);
                $freteId = (int) $pdo->lastInsertId();
            }
            $ins = $pdo->prepare('INSERT INTO frete_itens (frete_id, produto_id, quantidade) VALUES (?,?,?)');
            foreach ($itens as $it) $ins->execute([$freteId, (int) $it['produto_id'], (int) $it['quantidade']]);
            $pdo->commit();
            flash('success', $id > 0 ? 'Frete atualizado com sucesso.' : 'Frete nº ' . $freteId . ' gerado com sucesso.');
            redirect('fretes/ver.php?id=' . $freteId);
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $erros[] = 'Não foi possível salvar o frete. Tente novamente.';
        }
    }
}

$faltaCadastro = !$transportadores || count($enderecos) < 2 || !$produtos;
render_header($id ? 'Editar frete' : 'Gerar frete', 'fretes');
?>
<h1 class="h3 mb-3"><i class="bi bi-clipboard-check"></i> <?= $id ? 'Editar frete nº ' . $id : 'Gerar novo frete' ?></h1>
<?php if ($faltaCadastro): ?>
  <div class="alert alert-warning">Para gerar um frete, cadastre antes: pelo menos <strong>1 produto</strong>, <strong>2 endereços</strong> (origem e destino) e <strong>1 transportador</strong>.</div>
<?php endif; ?>
<?php foreach ($erros as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" novalidate>
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <div class="card mb-3"><div class="card-header bg-white fw-semibold">1. Dados do frete</div><div class="card-body">
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label required" for="transportador_id">Transportador</label>
        <select class="form-select" id="transportador_id" name="transportador_id">
          <option value="">Selecione</option>
          <?php foreach ($transportadores as $t): ?>
            <option value="<?= (int) $t['id'] ?>" <?= (string) $dados['transportador_id'] === (string) $t['id'] ? 'selected' : '' ?>><?= e($t['nome'] . ($t['placa'] ? ' - ' . $t['placa'] : '')) ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="col-md-3"><label class="form-label required" for="data_frete">Data</label>
        <input type="date" class="form-control" id="data_frete" name="data_frete" value="<?= e($dados['data_frete']) ?>"></div>
      <div class="col-md-3"><label class="form-label required" for="valor_frete">Valor do frete (R$)</label>
        <input type="number" step="0.01" min="0" class="form-control" id="valor_frete" name="valor_frete" value="<?= e($dados['valor_frete']) ?>"></div>
      <div class="col-md-6"><label class="form-label required" for="origem_id">Endereço de origem</label>
        <select class="form-select" id="origem_id" name="origem_id">
          <option value="">Selecione</option>
          <?php foreach ($enderecos as $a): ?>
            <option value="<?= (int) $a['id'] ?>" <?= (string) $dados['origem_id'] === (string) $a['id'] ? 'selected' : '' ?>><?= e($a['apelido'] . ' (' . $a['cidade'] . '/' . $a['uf'] . ')') ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="col-md-6"><label class="form-label required" for="destino_id">Endereço de destino</label>
        <select class="form-select" id="destino_id" name="destino_id">
          <option value="">Selecione</option>
          <?php foreach ($enderecos as $a): ?>
            <option value="<?= (int) $a['id'] ?>" <?= (string) $dados['destino_id'] === (string) $a['id'] ? 'selected' : '' ?>><?= e($a['apelido'] . ' (' . $a['cidade'] . '/' . $a['uf'] . ')') ?></option>
          <?php endforeach; ?>
        </select></div>
      <div class="col-12"><label class="form-label" for="observacoes">Observações</label>
        <input type="text" class="form-control" id="observacoes" name="observacoes" maxlength="255" value="<?= e($dados['observacoes']) ?>"></div>
    </div>
  </div></div>

  <div class="card mb-3"><div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span class="fw-semibold">2. Produtos transportados <small class="text-muted fw-normal">(um transportador pode levar vários produtos)</small></span>
    <button type="button" class="btn btn-sm btn-success" id="btn-add-item"><i class="bi bi-plus-lg"></i> Adicionar produto</button>
  </div><div class="card-body">
    <div id="itens">
      <?php foreach ($itens as $it): ?>
        <div class="row g-2 align-items-end mb-2 item-row">
          <div class="col-md-8"><label class="form-label small mb-0">Produto</label>
            <select class="form-select item-produto" name="item_produto[]">
              <option value="">Selecione</option>
              <?php foreach ($produtos as $p): ?>
                <option value="<?= (int) $p['id'] ?>" data-peso="<?= e($p['peso_kg']) ?>" data-valor="<?= e($p['valor']) ?>" <?= (string) $it['produto_id'] === (string) $p['id'] ? 'selected' : '' ?>><?= e($p['nome']) ?> (<?= e(kg($p['peso_kg'])) ?>)</option>
              <?php endforeach; ?>
            </select></div>
          <div class="col-6 col-md-2"><label class="form-label small mb-0">Quantidade</label>
            <input type="number" min="1" step="1" class="form-control item-qtd" name="item_qtd[]" value="<?= (int) $it['quantidade'] ?>"></div>
          <div class="col-6 col-md-2 text-end"><button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-trash"></i> Remover</button></div>
        </div>
      <?php endforeach; ?>
    </div>
    <hr>
    <div class="d-flex flex-wrap gap-4">
      <div><span class="text-muted">Peso total:</span> <strong id="total-peso">0,000 kg</strong></div>
      <div><span class="text-muted">Valor da carga:</span> <strong id="total-valor">R$ 0,00</strong></div>
    </div>
  </div></div>

  <template id="tpl-item">
    <div class="row g-2 align-items-end mb-2 item-row">
      <div class="col-md-8"><label class="form-label small mb-0">Produto</label>
        <select class="form-select item-produto" name="item_produto[]">
          <option value="">Selecione</option>
          <?php foreach ($produtos as $p): ?>
            <option value="<?= (int) $p['id'] ?>" data-peso="<?= e($p['peso_kg']) ?>" data-valor="<?= e($p['valor']) ?>"><?= e($p['nome']) ?> (<?= e(kg($p['peso_kg'])) ?>)</option>
          <?php endforeach; ?>
        </select></div>
      <div class="col-6 col-md-2"><label class="form-label small mb-0">Quantidade</label>
        <input type="number" min="1" step="1" class="form-control item-qtd" name="item_qtd[]" value="1"></div>
      <div class="col-6 col-md-2 text-end"><button type="button" class="btn btn-outline-danger btn-remove-item"><i class="bi bi-trash"></i> Remover</button></div>
    </div>
  </template>

  <div class="d-flex gap-2">
    <button class="btn btn-primary" <?= $faltaCadastro ? 'disabled' : '' ?>><i class="bi bi-check-lg"></i> <?= $id ? 'Salvar alterações' : 'Gerar frete' ?></button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
<?php render_footer(['assets/js/frete.js']); ?>
