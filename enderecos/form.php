<?php
require_once __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$campos = ['apelido', 'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];
$dados = array_fill_keys($campos, '');

if ($id > 0) {
    $st = db()->prepare('SELECT * FROM enderecos WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('danger', 'Endereço não encontrado.'); redirect('enderecos/index.php'); }
    $dados = $row;
}

$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($campos as $c) $dados[$c] = post($c);
    $dados['cep'] = digits($dados['cep']);
    $dados['uf'] = strtoupper($dados['uf']);

    if ($dados['apelido'] === '' || mb_strlen($dados['apelido']) > 80) $erros[] = 'Informe uma identificação (ex.: Depósito Central).';
    if (strlen($dados['cep']) !== 8) $erros[] = 'O CEP deve ter 8 dígitos.';
    if ($dados['logradouro'] === '' || mb_strlen($dados['logradouro']) > 150) $erros[] = 'Informe o logradouro.';
    if ($dados['numero'] === '' || mb_strlen($dados['numero']) > 20) $erros[] = 'Informe o número.';
    if (mb_strlen($dados['complemento']) > 80) $erros[] = 'Complemento muito longo (máx. 80).';
    if ($dados['bairro'] === '' || mb_strlen($dados['bairro']) > 80) $erros[] = 'Informe o bairro.';
    if ($dados['cidade'] === '' || mb_strlen($dados['cidade']) > 80) $erros[] = 'Informe a cidade.';
    if (!in_array($dados['uf'], UFS, true)) $erros[] = 'Selecione uma UF válida.';

    if (!$erros) {
        $params = [$dados['apelido'], $dados['cep'], $dados['logradouro'], $dados['numero'], $dados['complemento'] ?: null,
                   $dados['bairro'], $dados['cidade'], $dados['uf']];
        if ($id > 0) {
            db()->prepare('UPDATE enderecos SET apelido=?, cep=?, logradouro=?, numero=?, complemento=?, bairro=?, cidade=?, uf=? WHERE id=?')->execute([...$params, $id]);
            flash('success', 'Endereço atualizado com sucesso.');
        } else {
            db()->prepare('INSERT INTO enderecos (apelido, cep, logradouro, numero, complemento, bairro, cidade, uf) VALUES (?,?,?,?,?,?,?,?)')->execute($params);
            flash('success', 'Endereço cadastrado com sucesso.');
        }
        redirect('enderecos/index.php');
    }
}

render_header($id ? 'Editar endereço' : 'Novo endereço', 'enderecos');
?>
<h1 class="h3 mb-3"><i class="bi bi-geo-alt"></i> <?= $id ? 'Editar endereço #' . $id : 'Novo endereço' ?></h1>
<?php foreach ($erros as $er): ?><div class="alert alert-danger py-2"><?= e($er) ?></div><?php endforeach; ?>
<div class="card"><div class="card-body">
<form method="post" novalidate>
  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label required" for="apelido">Identificação</label>
      <input type="text" class="form-control" id="apelido" name="apelido" maxlength="80" placeholder="Ex.: Depósito Central" value="<?= e($dados['apelido']) ?>"></div>
    <div class="col-md-4"><label class="form-label required" for="cep">CEP</label>
      <input type="text" class="form-control" id="cep" name="cep" data-mask="cep" placeholder="00000-000" value="<?= e($dados['cep']) ?>"></div>
    <div class="col-md-8"><label class="form-label required" for="logradouro">Logradouro</label>
      <input type="text" class="form-control" id="logradouro" name="logradouro" maxlength="150" value="<?= e($dados['logradouro']) ?>"></div>
    <div class="col-md-2"><label class="form-label required" for="numero">Número</label>
      <input type="text" class="form-control" id="numero" name="numero" maxlength="20" value="<?= e($dados['numero']) ?>"></div>
    <div class="col-md-2"><label class="form-label" for="complemento">Compl.</label>
      <input type="text" class="form-control" id="complemento" name="complemento" maxlength="80" value="<?= e($dados['complemento']) ?>"></div>
    <div class="col-md-4"><label class="form-label required" for="bairro">Bairro</label>
      <input type="text" class="form-control" id="bairro" name="bairro" maxlength="80" value="<?= e($dados['bairro']) ?>"></div>
    <div class="col-md-5"><label class="form-label required" for="cidade">Cidade</label>
      <input type="text" class="form-control" id="cidade" name="cidade" maxlength="80" value="<?= e($dados['cidade']) ?>"></div>
    <div class="col-md-3"><label class="form-label required" for="uf">UF</label>
      <select class="form-select" id="uf" name="uf">
        <option value="">Selecione</option>
        <?php foreach (UFS as $u): ?><option value="<?= $u ?>" <?= $dados['uf'] === $u ? 'selected' : '' ?>><?= $u ?></option><?php endforeach; ?>
      </select></div>
  </div>
  <p class="form-text mt-2 mb-0">Dica: ao digitar o CEP, o sistema tenta preencher o endereço automaticamente (ViaCEP). A validade do CEP não é verificada.</p>
  <div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Salvar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>
</div></div>
<?php render_footer(); ?>
