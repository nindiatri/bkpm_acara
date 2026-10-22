<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/Autoload.php';

// Header default dan CORS (sesuaikan origin dengan frontend Anda)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

try {
    (new Router())->dispatch();
} catch (Throwable $e) {
    error_log($e->getMessage());
    Response::json(500, ['success' => false, 'message' => 'Terjadi kesalahan pada server']);
}