<?php

// =====================================================================
//  PENGATURAN DATABASE UNTUK INFINITYFREE
//  Ganti 4 nilai di bawah ini dengan data dari panel InfinityFree
//  (menu "MySQL Databases"). Tulis persis sama, perhatikan huruf
//  besar/kecil dan jangan ada spasi di awal/akhir.
// =====================================================================
if (!defined('DB_HOST')) define('DB_HOST', 'ISI_MYSQL_HOSTNAME');   // contoh: sql123.infinityfree.com
if (!defined('DB_NAME')) define('DB_NAME', 'ISI_NAMA_DATABASE');    // contoh: if0_12345678_workshop
if (!defined('DB_USER')) define('DB_USER', 'ISI_USERNAME');         // contoh: if0_12345678
if (!defined('DB_PASS')) define('DB_PASS', 'ISI_PASSWORD');         // password akun hosting InfinityFree

// Mode produksi: pesan JSON dibuat ringkas (tanpa pretty print).
if (!defined('APP_ENV')) {
    define('APP_ENV', 'production');
}

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = DB_HOST;
            $db = DB_NAME;
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('Koneksi database gagal: ' . $e->getMessage());
                Response::json(500, [
                    'success' => false,
                    'message' => 'Koneksi database gagal'
                ]);
            }
        }

        return self::$connection;
    }
}
