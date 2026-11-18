<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

// Caminho base do projeto (funciona em /htdocs/gerenciador-logistico ou em qualquer pasta)
$__root = str_replace('\\', '/', (string) realpath(__DIR__ . '/..'));
$__doc  = str_replace('\\', '/', (string) realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? '')));
define('BASE_URL', rtrim(substr($__root, strlen($__doc)), '/'));

const UFS = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

function e($v): string { return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return BASE_URL . '/' . ltrim($path, '/'); }
function redirect(string $path): void { header('Location: ' . url($path)); exit; }

function flash(string $type, string $msg): void { $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg]; }
function get_flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void
{
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
    if (!$ok) {
        http_response_code(400);
        exit('Requisição inválida (token CSRF).');
    }
}

function post(string $key): string { return trim((string) ($_POST[$key] ?? '')); }
function digits(string $s): string { return preg_replace('/\D+/', '', $s) ?? ''; }
function to_float(string $s): float { return (float) str_replace(',', '.', $s); }

function money($v): string { return 'R$ ' . number_format((float) $v, 2, ',', '.'); }
function kg($v): string { return number_format((float) $v, 3, ',', '.') . ' kg'; }
function fmt_date(string $d): string { $t = DateTime::createFromFormat('Y-m-d', $d); return $t ? $t->format('d/m/Y') : $d; }
function valid_date(string $d): bool
{
    $t = DateTime::createFromFormat('Y-m-d', $d);
    return $t !== false && $t->format('Y-m-d') === $d;
}

function fmt_cep(string $c): string { return strlen($c) === 8 ? substr($c, 0, 5) . '-' . substr($c, 5) : $c; }
function fmt_doc(string $d): string
{
    if (strlen($d) === 11) return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $d);
    if (strlen($d) === 14) return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $d);
    return $d;
}
function fmt_endereco(array $a): string
{
    $s = $a['logradouro'] . ', ' . $a['numero'];
    if (!empty($a['complemento'])) $s .= ' - ' . $a['complemento'];
    return $s . ' - ' . $a['bairro'] . ', ' . $a['cidade'] . '/' . $a['uf'] . ' - CEP ' . fmt_cep($a['cep']);
}

/** Cabeçalho + menu de navegação (Bootstrap 5). */
function render_header(string $title, string $active = ''): void
{
    $menu = [
        'inicio' => ['Início', 'index.php', 'bi-house'],
        'produtos' => ['Produtos', 'produtos/index.php', 'bi-box-seam'],
        'enderecos' => ['Endereços', 'enderecos/index.php', 'bi-geo-alt'],
        'transportadores' => ['Transportadores', 'transportadores/index.php', 'bi-truck'],
        'fretes' => ['Fretes', 'fretes/index.php', 'bi-clipboard-check'],
        'relatorios' => ['Relatórios', 'relatorios/index.php', 'bi-file-earmark-arrow-down'],
    ];
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> | Gerenciador Logístico</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= e(url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= e(url('index.php')) ?>"><i class="bi bi-truck-front"></i> Gerenciador Logístico</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Alternar menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto">
        <?php foreach ($menu as $key => [$label, $link, $icon]): ?>
          <li class="nav-item">
            <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="<?= e(url($link)) ?>"><i class="bi <?= e($icon) ?>"></i> <?= e($label) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container pb-5">
  <?php foreach (get_flashes() as $f): ?>
    <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert">
      <?= e($f['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
  <?php endforeach; ?>
<?php
}

function render_footer(array $scripts = []): void
{
    ?>
</main>
<footer class="text-center text-muted small py-3 border-top bg-white">Gerenciador Logístico &middot; Fundamentos de Programação para Internet</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(url('assets/js/app.js')) ?>"></script>
<?php foreach ($scripts as $s): ?>
<script src="<?= e(url($s)) ?>"></script>
<?php endforeach; ?>
</body>
</html>
<?php
}

/** Resumo dos fretes (com totais). Filtra por data ou por id. */
function fretes_resumo(?string $data = null, ?int $id = null): array
{
    $sql = "SELECT f.*, t.nome AS transportador, t.veiculo, t.placa,
              CONCAT(o.cidade, '/', o.uf) AS origem, CONCAT(d.cidade, '/', d.uf) AS destino,
              (SELECT COALESCE(SUM(i.quantidade),0) FROM frete_itens i WHERE i.frete_id = f.id) AS qtd_itens,
              (SELECT COALESCE(SUM(i.quantidade * p.peso_kg),0) FROM frete_itens i JOIN produtos p ON p.id = i.produto_id WHERE i.frete_id = f.id) AS peso_total,
              (SELECT COALESCE(SUM(i.quantidade * p.valor),0) FROM frete_itens i JOIN produtos p ON p.id = i.produto_id WHERE i.frete_id = f.id) AS valor_carga
            FROM fretes f
            JOIN transportadores t ON t.id = f.transportador_id
            JOIN enderecos o ON o.id = f.origem_id
            JOIN enderecos d ON d.id = f.destino_id";
    $where = [];
    $params = [];
    if ($data !== null) { $where[] = 'f.data_frete = ?'; $params[] = $data; }
    if ($id !== null)   { $where[] = 'f.id = ?';         $params[] = $id; }
    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
    $sql .= ' ORDER BY f.data_frete DESC, f.id DESC';
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function frete_itens(int $freteId): array
{
    $st = db()->prepare('SELECT i.*, p.nome, p.peso_kg, p.valor FROM frete_itens i JOIN produtos p ON p.id = i.produto_id WHERE i.frete_id = ? ORDER BY p.nome');
    $st->execute([$freteId]);
    return $st->fetchAll();
}
