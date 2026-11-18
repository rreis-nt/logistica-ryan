<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/pdf.php';

$data = (string) ($_GET['data'] ?? '');
$formato = (string) ($_GET['formato'] ?? 'pdf');
if (!valid_date($data) || !in_array($formato, ['pdf', 'csv'], true)) {
    flash('danger', 'Data ou formato inválido.');
    redirect('relatorios/index.php');
}
$fretes = fretes_resumo($data);
if (!$fretes) {
    flash('warning', 'Não há fretes nesta data.');
    redirect('relatorios/index.php?data=' . $data);
}
$nomeArquivo = 'relatorio-fretes-' . $data;

if ($formato === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '.csv"');
    echo "\xEF\xBB\xBF"; // BOM para o Excel reconhecer acentos
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Frete', 'Data', 'Transportador', 'Origem', 'Destino', 'Produto', 'Quantidade', 'Peso unitário (kg)', 'Valor unitário (R$)', 'Valor do frete (R$)'], ';');
    foreach ($fretes as $f) {
        foreach (frete_itens((int) $f['id']) as $i) {
            fputcsv($out, [$f['id'], fmt_date($f['data_frete']), $f['transportador'], $f['origem'], $f['destino'], $i['nome'], $i['quantidade'],
                number_format((float) $i['peso_kg'], 3, ',', ''), number_format((float) $i['valor'], 2, ',', ''), number_format((float) $f['valor_frete'], 2, ',', '')], ';');
        }
    }
    fclose($out);
    exit;
}

// PDF
$pdf = new SimplePDF();
$pdf->line('Relatório de Fretes', 18, true);
$pdf->line('Data dos fretes: ' . fmt_date($data) . '   |   Gerado em: ' . date('d/m/Y H:i'), 10);
$pdf->rule();
$totalGeral = 0.0;
foreach ($fretes as $f) {
    $st = db()->prepare('SELECT * FROM enderecos WHERE id = ?');
    $st->execute([$f['origem_id']]);  $o = $st->fetch();
    $st->execute([$f['destino_id']]); $d = $st->fetch();
    $pdf->space(6);
    $veic = trim(($f['veiculo'] ?? '') . ' ' . ($f['placa'] ?? ''));
    $pdf->line('Frete nº ' . $f['id'] . ' - ' . $f['transportador'] . ($veic !== '' ? ' (' . $veic . ')' : ''), 11, true);
    $pdf->line('Origem: ' . fmt_endereco($o), 9, false, 10);
    $pdf->line('Destino: ' . fmt_endereco($d), 9, false, 10);
    foreach (frete_itens((int) $f['id']) as $i) {
        $pdf->line('- ' . $i['quantidade'] . ' x ' . $i['nome'] . ' (' . kg($i['peso_kg']) . ' cada) = ' . money($i['valor'] * $i['quantidade']), 9, false, 18);
    }
    $pdf->line('Peso total: ' . kg($f['peso_total']) . '   |   Valor da carga: ' . money($f['valor_carga']) . '   |   Valor do frete: ' . money($f['valor_frete']), 9, true, 10);
    if (!empty($f['observacoes'])) $pdf->line('Obs.: ' . $f['observacoes'], 9, false, 10);
    $pdf->rule();
    $totalGeral += (float) $f['valor_frete'];
}
$pdf->space(6);
$pdf->line('Total de fretes: ' . count($fretes) . '   |   Soma dos valores de frete: ' . money($totalGeral), 11, true);

$bin = $pdf->output();
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $nomeArquivo . '.pdf"');
header('Content-Length: ' . strlen($bin));
echo $bin;
