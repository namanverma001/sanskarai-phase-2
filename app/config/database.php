<?php
/**
 * Sanskar AI - Database Configuration
 * ====================================
 * Centralized PDO database connection with environment variable support
 * Supports future scaling with read replicas
 */

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    private static ?PDO $readConnection = null;

    // Connection configuration
    private static array $config = [];

    /**
     * Load environment variables from .env file
     */
    public static function loadEnv(): void
    {
        $envPath = dirname(__DIR__, 2) . '/.env';

        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse key=value
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value, " \t\n\r\0\x0B\"'");

                    // Set as environment variable
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }

        // Store configuration using whichever environment source is available
        self::$config = [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: '3306',
            'database' => getenv('DB_DATABASE') ?: 'SAI',
            'username' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ];
    }

    /**
     * Get configuration value
     */
    public static function getConfig(?string $key = null)
    {
        if (empty(self::$config)) {
            self::loadEnv();
        }

        if ($key === null) {
            return self::$config;
        }

        return self::$config[$key] ?? null;
    }

    /**
     * Get PDO connection instance (Singleton pattern)
     * 
     * @param bool $createDatabase Whether to create database if not exists
     * @return PDO
     */
    public static function getConnection(bool $createDatabase = false): PDO
    {
        if (self::$connection === null) {
            self::loadEnv();

            try {
                $dsn = sprintf(
                    "mysql:host=%s;port=%s;charset=%s",
                    self::$config['host'],
                    self::$config['port'],
                    self::$config['charset']
                );

                if (!$createDatabase) {
                    $dsn .= ";dbname=" . self::$config['database'];
                }

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$connection = new PDO(
                    $dsn,
                    self::$config['username'],
                    self::$config['password'],
                    $options
                );

            } catch (PDOException $e) {
                self::logError("Database connection failed: " . $e->getMessage());
                die("Database connection failed. Please check your configuration.");
            }
        }

        return self::$connection;
    }

    /**
     * Get read-only connection for scaling (future use)
     * 
     * @return PDO
     */
    public static function getReadConnection(): PDO
    {
        if (self::$readConnection === null) {
            // For now, use the same connection
            // In production, this can point to read replicas
            self::$readConnection = self::getConnection();
        }

        return self::$readConnection;
    }

    /**
     * Create database if not exists
     */
    public static function createDatabase(): bool
    {
        try {
            $pdo = self::getConnection(true);
            $database = self::$config['database'];

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` 
                        CHARACTER SET utf8mb4 
                        COLLATE utf8mb4_unicode_ci");

            $pdo->exec("USE `$database`");

            return true;

        } catch (PDOException $e) {
            self::logError("Failed to create database: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute a query and return results
     */
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute a query and return single result
     */
    public static function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Execute an insert/update/delete query
     */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Get last inserted ID
     */
    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Log database errors
     */
    private static function logError(string $message): void
    {
        $logPath = dirname(__DIR__, 2) . '/storage/logs/database.log';
        $logDir = dirname($logPath);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;

        file_put_contents($logPath, $logMessage, FILE_APPEND);
    }

    /**
     * Close connections
     */
    public static function close(): void
    {
        self::$connection = null;
        self::$readConnection = null;
    }
}
