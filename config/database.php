<?php
// Configurações padrão do XAMPP: usuário root sem senha.
const DB_HOST = 'localhost';
const DB_NAME = 'gerenciador_logistico';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $ex) {
            http_response_code(500);
            exit('Erro ao conectar no banco. Inicie o MySQL no XAMPP e importe database/schema.sql. Detalhe: '
                . htmlspecialchars($ex->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
    }
    return $pdo;
}
