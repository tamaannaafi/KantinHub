<?php
require __DIR__ . '/bootstrap.php';

function shape_order(array $o): array
{
    $o['id']           = (int)$o['id'];
    $o['kantin_id']    = (int)$o['kantin_id'];
    $o['total_price']  = (int)$o['total_price'];
    $o['is_completed'] = (bool)$o['is_completed'];
    return $o;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['all'])) {
        require_admin();
        $rows = $pdo->query(
            'SELECT o.*, k.nama AS kantin_nama
             FROM orders o
             LEFT JOIN kantin k ON k.id = o.kantin_id
             ORDER BY o.created_at DESC, o.id DESC'
        )->fetchAll();
    } else {
        $stmt = $pdo->prepare(
            'SELECT * FROM orders WHERE username = ? ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute([trim($_GET['username'] ?? '')]);
        $rows = $stmt->fetchAll();
    }

    respond(with_iso_dates(array_map('shape_order', $rows)));
}

if ($method !== 'POST') {
    fail('Method tidak diizinkan.', 405);
}

$in     = input();
$action = $in['action'] ?? '';

if ($action === 'create') {
    $kantinId = (int)($in['kantin_id'] ?? 0);
    $username = trim($in['username'] ?? '');
    $items    = trim($in['items'] ?? '');
    $total    = (int)($in['total_price'] ?? 0);
    $payment  = $in['payment_method'] ?? 'Tunai';
    $note     = trim($in['note'] ?? '');

    if (!$kantinId || $username === '' || $items === '' || $total <= 0) {
        fail('Data pesanan tidak lengkap.');
    }
    if (!in_array($payment, ['Tunai', 'QRIS'], true)) {
        fail('Metode pembayaran tidak valid.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO orders (kantin_id, username, items, total_price, payment_method, note)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $kantinId,
        mb_substr($username, 0, 50),
        $items,
        $total,
        $payment,
        $note === '' ? null : mb_substr($note, 0, 500),
    ]);
    respond(['id' => (int)$pdo->lastInsertId()], 201);
}

if ($action === 'complete') {
    require_admin();
    $id = (int)($in['id'] ?? 0);
    if (!$id) {
        fail('ID pesanan tidak valid.');
    }

    $stmt = $pdo->prepare('UPDATE orders SET is_completed = 1 WHERE id = ?');
    $stmt->execute([$id]);
    respond(['ok' => true]);
}

fail('Action tidak dikenal.');
