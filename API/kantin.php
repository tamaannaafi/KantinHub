<?php
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fail('Method tidak diizinkan.', 405);
}

$rows = $pdo->query('SELECT id, nama, icon FROM kantin ORDER BY id ASC')->fetchAll();
respond($rows);
