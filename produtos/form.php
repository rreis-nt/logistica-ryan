<?php
require_once __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$dados = ['nome' => '', 'descricao' => '', 'peso_kg' => '', 'valor' => ''];

if ($id > 0) {
    $st = db()->prepare('SELECT * FROM produtos WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('danger', 'Produto não encontrado.'); redirect('produtos/index.php'); }
    $dados = $row;
}

$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $dados = [
        'nome' => post('nome'), 'descricao' => post('descricao'),
        'peso_kg' => post('peso_kg'), 'valor' => post('valor'),
    ];
    if ($dados['nome'] === '' || mb_strlen($dados['nome']) > 120) $erros[] = 'Informe o nome do produto (até 120 caracteres).';
    if (mb_strlen($dados['descricao']) > 255) $erros[] = 'A descrição pode ter no máximo 255 caracteres.';
    if (!is_numeric(str_replace(',', '.', $dados['peso_kg'])) || to_float($dados['peso_kg']) <= 0) $erros[] = 'Informe um peso maior que zero.';
    if (!is_numeric(str_replace(',', '.', $dados['valor'])) || to_float($dados['valor']) < 0) $erros[] = 'Informe um valor válido (zero ou maior).';

    if (!$erros) {
        $params = [$dados['nome'], $dados['descricao'] ?: null, to_float($dados['peso_kg']), to_float($dados['valor'])];
        if ($id > 0) {
            db()->prepare('UPDATE produtos SET nome=?, descricao=?, peso_kg=?, valor=? WHERE id=?')->execute([...$params, $id]);
            flash('success', 'Produto atualizado com sucesso.');
        } else {
            db()->prepare('INSERT INTO produtos (nome, descricao, peso_kg, valor) VALUES (?,?,?,?)')->execute($params);
            flash('success', 'Produto cadastrado com sucesso.');
        }
        redirect('produtos/index.php');
    }
}

render_header($id ? 'Editar produto' : 'Novo produto', 'produtos');
?>
<h1 class="h3 mb-3"><i class="bi bi-box-seam"></i> <?= $id ? 'Editar produto #' . $id : 'Novo produto' ?></h1>
<?php foreach ($erros as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>
<div class="card"><div class="card-body">
<form method="post" novalidate>
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label required" for="nome">Nome</label>
      <input type="text" class="form-control" id="nome" name="nome" maxlength="120" required value="<?= e($dados['nome']) ?>"></div>
    <div class="col-md-4"><label class="form-label required" for="peso_kg">Peso (kg)</label>
      <input type="number" step="0.001" min="0.001" class="form-control" id="peso_kg" name="peso_kg" required value="<?= e($dados['peso_kg']) ?>"></div>
    <div class="col-md-8"><label class="form-label" for="descricao">Descrição</label>
      <input type="text" class="form-control" id="descricao" name="descricao" maxlength="255" value="<?= e($dados['descricao']) ?>"></div>
    <div class="col-md-4"><label class="form-label required" for="valor">Valor unitário (R$)</label>
      <input type="number" step="0.01" min="0" class="form-control" id="valor" name="valor" required value="<?= e($dados['valor']) ?>"></div>
  </div>
  <div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
</div></div>
<?php render_footer(); ?>
