<?php

namespace MythicalSystemsFramework\Database;

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Kernel\Logger;
use PDO;
use PDOException;
use mysqli;

class MySQL
{
    private static $connection;

    /**
     * Connects to the database server using PDO.
     * 
     * @return PDO The PDO object representing the database connection.
     * @throws PDOException If the connection to the database fails.
     */
    public function connectPDO(): PDO
    {

        $dsn = "mysql:host=" . cfg::get("database", "host") . ";dbname=" . cfg::get("database", "name") . ";port=" . cfg::get("database", "port") . ";charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, cfg::get("database", "username"), cfg::get("database", "password"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "Failed to connect to the database!");
            throw new PDOException("Failed to connect to the database: " . $e->getMessage());
        }
    }

    /**
     * Connects to the database server using MYSQLI.
     * 
     * @return mysqli
     */
    public function connectMYSQLI(): mysqli
    {
        if (!isset(self::$connection)) {
            self::$connection = new mysqli(
                cfg::get("database", "host"),
                cfg::get("database", "username"),
                cfg::get("database", "password"),
                cfg::get("database", "name"),
                cfg::get("database", "port")
            );

            if (self::$connection->connect_error) {
                Logger::log(Logger::CRITICAL, Logger::DATABASE, "Failed to connect to the database!");
            }
        }

        return self::$connection;
    }

    /**
     * Close a database connection if open
     * 
     * @return void
     */
    public static function closeConnection(): void
    {
        if (isset(self::$connection)) {
            self::$connection->close();
            self::$connection = null;
        }
    }

    /**
     * Tries to establish a connection to the database server.
     *
     * @param string $host The host
     * @param string|int $port The port
     * @param string $username The username
     * @param string $password The password
     * @param string $database The database name
     * 
     * @return bool True if the connection is successful, false otherwise.
     * @throws PDOException If the connection to the database fails.     * 
     */
    public function tryConnection(string $host, string|int $port, string $username, string $password, string $database): bool
    {
        try {
            $dsn = "mysql:host=" . $host . ";dbname=" . $database . ";port=" . $port . ";charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        } catch (PDOException $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "Failed to execute PDO query: " . $e);
            return false;
        }
    }
}