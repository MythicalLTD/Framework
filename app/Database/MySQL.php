<?php

namespace MythicalSystemsFramework\Database;

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use PDO;
use PDOException;
use PDOStatement;

class MySQL {
    /**
     * Connects to the database server using PDO.
     * 
     * @return PDO The PDO object representing the database connection.
     * @throws PDOException If the connection to the database fails.
     */
    public function connect(): PDO {

        $dsn = "mysql:host=".cfg::get("database","host").";dbname=".cfg::get("database","name").";port=".cfg::get("database","port").";charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, cfg::get("database","username"), cfg::get("database","password"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException("Failed to connect to the database: " . $e->getMessage());
        }
    }

    /**
     * Tries to establish a connection to the database server.
     * 
     * @return bool True if the connection is successful, false otherwise.
     */
    public function tryConnection(): bool {
        try {
            $this->connect();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Executes a SQL query on the database.
     *
     * @param PDO $pdo The PDO object representing the database connection.
     * @param string $query The SQL query to execute.
     * @param array $params The parameters to bind to the query.
     * 
     * @return PDOStatement The PDOStatement object representing the result of the query.
     * @throws PDOException If the query execution fails.
     */
    public function executeQuery(PDO $pdo, string $query, array $params = []): PDOStatement {
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new PDOException("Failed to execute query: " . $e->getMessage());
        }
    }

    /**
     * Closes the database connection.
     *
     * @param PDO $pdo The PDO object representing the database connection.
     */
    public function closeConnection(PDO $pdo) {
        $pdo = null;
    }
}
