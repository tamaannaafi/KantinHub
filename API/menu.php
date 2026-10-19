<?php
// GET  api/menu.php?kantin_id=1  -> menu milik satu kantin   (publik)
// GET  api/menu.php?all=1        -> semua menu + nama kantin (admin)
// POST api/menu.php              -> tambah / ubah foto / hapus menu (admin)
require __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];

// ---------- BACA DATA ----------
if ($method === 'GET') {
    if (isset($_GET['all'])) {
        require_admin();
        $rows = $pdo->query(
            'SELECT m.*, k.nama AS kantin_nama
             FROM menu m
             LEFT JOIN kantin k ON k.id = m.kantin_id
             ORDER BY m.kantin_id ASC, m.id ASC'
        )->fetchAll();
        respond($rows);
    }

    $stmt = $pdo->prepare('SELECT * FROM menu WHERE kantin_id = ? ORDER BY id ASC');
    $stmt->execute([(int)($_GET['kantin_id'] ?? 0)]);
    respond($stmt->fetchAll());
}

// ---------- UBAH DATA (khusus admin) ----------
if ($method !== 'POST') {
    fail('Method tidak diizinkan.', 405);
}

require_admin();
$in     = input();
$action = $in['action'] ?? '';

if ($action === 'add') {
    $kantinId = (int)($in['kantin_id'] ?? 0);
    $nama     = trim($in['nama'] ?? '');
    $harga    = (int)($in['harga'] ?? 0);
    $img      = trim($in['img'] ?? '');

    if (!$kantinId || $nama === '' || $harga <= 0 || $img === '') {
        fail('Semua field harus diisi dengan benar!');
    }
    if (!preg_match('#^https?://#i', $img)) {
        fail('URL foto harus diawali http:// atau https://');
    }

    $stmt = $pdo->prepare('INSERT INTO menu (kantin_id, nama, harga, img) VALUES (?, ?, ?, ?)');
    $stmt->execute([$kantinId, mb_substr($nama, 0, 100), $harga, $img]);
    respond(['id' => (int)$pdo->lastInsertId()], 201);
}

if ($action === 'update_photo') {
    $id  = (int)($in['id'] ?? 0);
    $img = trim($in['img'] ?? '');

    if (!$id || !preg_match('#^https?://#i', $img)) {
        fail('URL foto harus diawali http:// atau https://');
    }

    $stmt = $pdo->prepare('UPDATE menu SET img = ? WHERE id = ?');
    $stmt->execute([$img, $id]);
    respond(['ok' => true]);
}

if ($action === 'delete') {
    $id = (int)($in['id'] ?? 0);
    if (!$id) {
        fail('ID menu tidak valid.');
    }

    $stmt = $pdo->prepare('DELETE FROM menu WHERE id = ?');
    $stmt->execute([$id]);
    respond(['ok' => true]);
}

fail('Action tidak dikenal.');
