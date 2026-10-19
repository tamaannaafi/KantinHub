<?php
require_once __DIR__ . '/config.php';

date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

ini_set('session.gc_maxlifetime', '86400');
session_set_cookie_params([
    'lifetime' => 86400,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

function respond($data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function fail(string $message, int $status = 400): void
{
    respond(['error' => $message], $status);
}

function input(): array
{
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function require_admin(): void
{
    if (empty($_SESSION['is_admin'])) {
        fail('Akses ditolak. Silakan login sebagai admin.', 403);
    }
}

function with_iso_dates(array $rows): array
{
    return array_map(function ($row) {
        if (isset($row['created_at'])) {
            $row['created_at'] = (new DateTime($row['created_at']))->format(DATE_ATOM);
        }
        return $row;
    }, $rows);
}

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    fail(DEBUG ? $e->getMessage() : 'Terjadi kesalahan pada server.', 500);
});

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+07:00'",
    ]
);
