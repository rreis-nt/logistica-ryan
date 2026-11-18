<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('fretes/index.php');
csrf_check();
db()->prepare('DELETE FROM fretes WHERE id = ?')->execute([(int) post('id')]);  // itens saem por ON DELETE CASCADE
flash('success', 'Frete excluído.');
redirect('fretes/index.php');
