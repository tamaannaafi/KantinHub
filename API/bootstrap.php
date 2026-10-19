<?php
// ============================================================
// BOOTSTRAP - dipanggil di awal SETIAP file endpoint (kantin.php, menu.php, dst.)
// Isinya: koneksi database + fungsi-fungsi bantu supaya kode endpoint tetap pendek.
// ============================================================

require_once __DIR__ . '/config.php';

date_default_timezone_set('Asia/Jakarta');            // WIB
header('Content-Type: application/json; charset=utf-8'); // semua jawaban berupa JSON

// --- Session: dipakai untuk mengingat bahwa admin sudah login ---
ini_set('session.gc_maxlifetime', '86400');           // sesi admin bertahan 1 hari
session_set_cookie_params([
    'lifetime' => 86400,
    'path'     => '/',
    'httponly' => true,    // cookie tidak bisa dibaca JavaScript
    'samesite' => 'Lax',
]);
session_start();

// --- Fungsi bantu ---

// Kirim data ke browser sebagai JSON lalu hentikan script
function respond($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Kirim pesan error sebagai JSON: {"error": "..."}
function fail(string $message, int $status = 400): void
{
    respond(['error' => $message], $status);
}

// Baca data JSON yang dikirim browser lewat fetch(..., { body: JSON.stringify(...) })
function input(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

// Hanya admin yang boleh lewat. Kalau bukan admin -> error 403 dan script berhenti.
function require_admin(): void
{
    if (empty($_SESSION['is_admin'])) {
        fail('Akses ditolak. Silakan login sebagai admin.', 403);
    }
}

// Ubah created_at dari MySQL ("2026-10-19 14:30:00") menjadi format ISO
// ("2026-10-19T14:30:00+07:00") supaya new Date(...) di JavaScript membacanya
// dengan benar di semua browser (termasuk Safari).
function with_iso_dates(array $rows): array
{
    return array_map(function ($row) {
        if (isset($row['created_at'])) {
            $row['created_at'] = (new DateTime($row['created_at']))->format(DATE_ATOM);
        }
        return $row;
    }, $rows);
}

// --- Tangkap semua error yang tidak terduga (misal MySQL mati / query salah) ---
set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    fail(DEBUG ? $e->getMessage() : 'Terjadi kesalahan pada server.', 500);
});

// --- Koneksi ke database memakai PDO ---
$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // error jadi Exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // hasil berupa array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                  // prepared statement asli (lebih aman & tipe data benar)
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'", // jam MySQL = WIB
    ]
);
