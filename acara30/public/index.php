<?php
declare(strict_types=1);

// Acara 30 Langkah 3: Penguatan konfigurasi PHP Lingkungan Produksi
// Nonaktifkan display_errors dan aktifkan log_errors agar pesan error sensitif tidak bocor ke client
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');

// Acara 24: header CORS dipasang satu kali di Front Controller,
// sebelum Router dijalankan, sehingga berlaku untuk semua resource
// (mahasiswa, buku, login, register).
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../app/core/Autoload.php';

try {
    (new Router())->dispatch();
} catch (Throwable $e) {
    error_log($e->getMessage());
    Response::json(500, ['success' => false, 'message' => 'Terjadi kesalahan pada server']);
}
