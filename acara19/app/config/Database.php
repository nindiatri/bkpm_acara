<?php

// Ubah menjadi 'production' saat aplikasi dipasang di server produksi.
if (!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = 'localhost';
            $db = 'db_workshop';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$connection = new PDO($dsn, 'root', '', $options);
            } catch (PDOException $e) {
                Response::json(500, [
                    'success' => false,
                    'message' => 'Koneksi database gagal'
                ]);
            }
        }

        return self::$connection;
    }
}