<?php
require_once __DIR__ . '/config/db.php';
$stmt = $pdo->query('SELECT id, name, category, price, stock FROM products');
$rows = $stmt->fetchAll();
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
