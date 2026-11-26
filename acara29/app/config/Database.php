<?php

// =====================================================================
//  PENGATURAN DATABASE UNTUK INFINITYFREE
// =====================================================================
if (!defined('DB_HOST')) define('DB_HOST', 'sql302.infinityfree.com');   
if (!defined('DB_NAME')) define('DB_NAME', 'if0_43502835_dbworkshop');    
if (!defined('DB_USER')) define('DB_USER', 'if0_43502835');        
if (!defined('DB_PASS')) define('DB_PASS', 'nindi277');        

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
