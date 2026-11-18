<?php
require_once __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$campos = ['nome', 'documento', 'telefone', 'email', 'veiculo', 'placa'];
$dados = array_fill_keys($campos, '');

if ($id > 0) {
    $st = db()->prepare('SELECT * FROM transportadores WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('danger', 'Transportador não encontrado.'); redirect('transportadores/index.php'); }
    $dados = $row;
}

$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($campos as $c) $dados[$c] = post($c);
    $dados['documento'] = digits($dados['documento']);   // CPF/CNPJ: sem validar autenticidade (conforme o roteiro)
    $dados['telefone'] = digits($dados['telefone']);
    $dados['placa'] = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $dados['placa']) ?? '');

    if ($dados['nome'] === '' || mb_strlen($dados['nome']) > 120) $erros[] = 'Informe o nome do transportador.';
    if (!in_array(strlen($dados['documento']), [11, 14], true)) $erros[] = 'Informe um CPF (11 dígitos) ou CNPJ (14 dígitos).';
    if ($dados['telefone'] !== '' && !in_array(strlen($dados['telefone']), [10, 11], true)) $erros[] = 'Telefone deve ter 10 ou 11 dígitos (com DDD).';
    if ($dados['email'] !== '' && (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($dados['email']) > 120)) $erros[] = 'E-mail inválido.';
    if (mb_strlen($dados['veiculo']) > 60) $erros[] = 'Veículo muito longo (máx. 60).';
    if (strlen($dados['placa']) > 7) $erros[] = 'Placa inválida.';

    if (!$erros) {
        $fmtPlaca = $dados['placa'] ?: null;
        $params = [$dados['nome'], $dados['documento'], $dados['telefone'] ?: null, $dados['email'] ?: null, $dados['veiculo'] ?: null, $fmtPlaca];
        if ($id > 0) {
            db()->prepare('UPDATE transportadores SET nome=?, documento=?, telefone=?, email=?, veiculo=?, placa=? WHERE id=?')->execute([...$params, $id]);
            flash('success', 'Transportador atualizado com sucesso.');
        } else {
            db()->prepare('INSERT INTO transportadores (nome, documento, telefone, email, veiculo, placa) VALUES (?,?,?,?,?,?)')->execute($params);
            flash('success', 'Transportador cadastrado com sucesso.');
        }
        redirect('transportadores/index.php');
    }
}

render_header($id ? 'Editar transportador' : 'Novo transportador', 'transportadores');
?>
<h1 class="h3 mb-3"><i class="bi bi-truck"></i> <?= $id ? 'Editar transportador #' . $id : 'Novo transportador' ?></h1>
<?php foreach ($erros as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>
<div class="card"><div class="card-body">
<form method="post" novalidate>
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label required" for="nome">Nome / Razão social</label>
      <input type="text" class="form-control" id="nome" name="nome" maxlength="120" value="<?= e($dados['nome']) ?>"></div>
    <div class="col-md-4"><label class="form-label required" for="documento">CPF ou CNPJ</label>
      <input type="text" class="form-control" id="documento" name="documento" data-mask="doc" value="<?= e($dados['documento']) ?>"></div>
    <div class="col-md-4"><label class="form-label" for="telefone">Telefone</label>
      <input type="text" class="form-control" id="telefone" name="telefone" data-mask="tel" placeholder="(00) 00000-0000" value="<?= e($dados['telefone']) ?>"></div>
    <div class="col-md-8"><label class="form-label" for="email">E-mail</label>
      <input type="email" class="form-control" id="email" name="email" maxlength="120" value="<?= e($dados['email']) ?>"></div>
    <div class="col-md-8"><label class="form-label" for="veiculo">Veículo</label>
      <input type="text" class="form-control" id="veiculo" name="veiculo" maxlength="60" placeholder="Ex.: Caminhão baú" value="<?= e($dados['veiculo']) ?>"></div>
    <div class="col-md-4"><label class="form-label" for="placa">Placa</label>
      <input type="text" class="form-control" id="placa" name="placa" data-mask="placa" placeholder="ABC1D23" value="<?= e($dados['placa']) ?>"></div>
  </div>
  <div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
</div></div>
<?php render_footer(); ?>
