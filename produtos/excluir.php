<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('produtos/index.php');
csrf_check();
try {
    db()->prepare('DELETE FROM produtos WHERE id = ?')->execute([(int) post('id')]);
    flash('success', 'Produto excluído.');
} catch (PDOException $ex) {
    flash('danger', 'Não é possível excluir: o produto está em um ou mais fretes.');
}
redirect('produtos/index.php');
