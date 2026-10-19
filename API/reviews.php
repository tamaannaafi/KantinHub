<?php
// GET  api/reviews.php  -> 5 ulasan terbaru
// POST api/reviews.php  -> kirim ulasan baru  { username, review_text }
require __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rows = $pdo->query(
        'SELECT id, username, review_text, created_at
         FROM reviews
         ORDER BY created_at DESC, id DESC
         LIMIT 5'
    )->fetchAll();
    respond(with_iso_dates($rows));
}

if ($method === 'POST') {
    $in       = input();
    $username = trim($in['username'] ?? '') ?: 'Anonim';
    $text     = trim($in['review_text'] ?? '');

    if ($text === '') {
        fail('Ulasan tidak boleh kosong!');
    }

    $stmt = $pdo->prepare('INSERT INTO reviews (username, review_text) VALUES (?, ?)');
    $stmt->execute([mb_substr($username, 0, 50), mb_substr($text, 0, 500)]);
    respond(['ok' => true], 201);
}

fail('Method tidak diizinkan.', 405);
