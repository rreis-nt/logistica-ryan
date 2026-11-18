<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('transportadores/index.php');
csrf_check();
try {
    db()->prepare('DELETE FROM transportadores WHERE id = ?')->execute([(int) post('id')]);
    flash('success', 'Transportador excluído.');
} catch (PDOException $ex) {
    flash('danger', 'Não é possível excluir: o transportador possui fretes.');
}
redirect('transportadores/index.php');
