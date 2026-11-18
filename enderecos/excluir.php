<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('enderecos/index.php');
csrf_check();
try {
    db()->prepare('DELETE FROM enderecos WHERE id = ?')->execute([(int) post('id')]);
    flash('success', 'Endereço excluído.');
} catch (PDOException $ex) {
    flash('danger', 'Não é possível excluir: o endereço é origem ou destino de algum frete.');
}
redirect('enderecos/index.php');
