<?php
// POST api/auth.php  { action: "login", username, password }  -> { is_admin: true/false }
// POST api/auth.php  { action: "logout" }
require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail('Method tidak diizinkan.', 405);
}

$in     = input();
$action = $in['action'] ?? '';

if ($action === 'login') {
    $username = trim($in['username'] ?? '');
    $password = $in['password'] ?? '';

    if ($username === '') {
        fail('Username tidak boleh kosong.');
    }

    // Login admin: password dicek DI SERVER, bukan di browser
    if ($username === ADMIN_USERNAME) {
        if (!password_verify($password, ADMIN_PASSWORD_HASH)) {
            fail('Password admin salah.', 401);
        }
        session_regenerate_id(true);
        $_SESSION['is_admin'] = true;
        respond(['is_admin' => true]);
    }

    // User biasa: sama seperti sebelumnya, cukup isi nama
    $_SESSION['is_admin'] = false;
    respond(['is_admin' => false]);
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    respond(['ok' => true]);
}

fail('Action tidak dikenal.');
