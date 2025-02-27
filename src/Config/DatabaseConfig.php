<?php

namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DatabaseConfig {
    private static ?PDO $conn = null;

    public function __construct() {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public static function connect(): ?PDO {
        try {
            $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            self::$conn = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
            return self::$conn;
        } catch (PDOException $e) {
            exit("Database Connection Error: " . $e->getMessage());
        }
    }

    public static function close(): void
    {
        self::$pdo = null; // This explicitly closes the connection
    }
}
