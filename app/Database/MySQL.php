<?php

namespace MythicalSystemsFramework\Database;

use PDO;
use mysqli;
use MythicalSystemsFramework\Cli\Kernel;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class MySQL
{
    private static $connection;
    public static int $migrated_files_count;

    /**
     * Connects to the database server using PDO.
     *
     * @return \PDO the PDO object representing the database connection
     *
     * @throws \Exception if the connection to the database fails
     */
    public function connectPDO(): \PDO
    {
        $dsn = 'mysql:host=' . cfg::get('database', 'host') . ';dbname=' . cfg::get('database', 'name') . ';port=' . cfg::get('database', 'port') . ';charset=utf8mb4';
        try {
            $pdo = new \PDO($dsn, cfg::get('database', 'username'), cfg::get('database', 'password'));
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to connect to the database!');
            throw new \Exception('Failed to connect to the database: ' . $e->getMessage());
        }
    }

    /**
     * Connects to the database server using MYSQLI.
     */
    public function connectMYSQLI(): \mysqli
    {
        if (!isset(self::$connection)) {
            self::$connection = new \mysqli(
                cfg::get('database', 'host'),
                cfg::get('database', 'username'),
                cfg::get('database', 'password'),
                cfg::get('database', 'name'),
                cfg::get('database', 'port')
            );

            if (self::$connection->connect_error) {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to connect to the database!');
            }
        }

        return self::$connection;
    }

    /**
     * Close a database connection if open.
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
     * @return bool true if the connection is successful, false otherwise
     *
     * @throws \Exception If the connection to the database fails.     *
     */
    public function tryConnection(string $host, string|int $port, string $username, string $password, string $database): bool
    {
        try {
            $dsn = 'mysql:host=' . $host . ';dbname=' . $database . ';port=' . $port . ';charset=utf8mb4';
            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to execute PDO query: ' . $e);

            return false;
        }
    }

    /**
     * Try to lock a record!
     *
     * @param string $table The table name!
     * @param string $id The record id!
     */
    public static function requestLock(string $table, string $id): void
    {
        try {
            if (self::doesTableExist($table) === false) {
                return;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("UPDATE `$table` SET `locked` = 'true' WHERE `id` = ?;");
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to lock table: ' . $e);

            return;
        }
    }

    /**
     * Try to unlock a record.
     * Unlock a record so you can write and read it!
     *
     * @param string $table The table name!
     * @param string $id The id of the record!
     */
    public static function requestUnLock(string $table, string $id): void
    {
        try {
            if (self::doesTableExist($table) === false) {
                return;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("UPDATE `$table` SET `locked` = 'false' WHERE `id` = ?;");
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to unlock table: ' . $e);

            return;
        }
    }

    /**
     * Get the lock status of a record.
     *
     * @param string $table The table name
     * @param string $id The id of the record
     */
    public static function getLock(string $table, string $id): bool
    {
        try {
            if (self::doesTableExist($table) === false) {
                return false;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('SELECT `locked` FROM `' . $table . '` WHERE `id` = ?;');
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return $row['locked'];
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to get lock status: ' . $e);

            return false;
        }
    }

    /**
     * Does a table exist in the database?
     *
     * @param string $table The table name
     */
    public static function doesTableExist(string $table): bool
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $conn->query('SELECT * FROM ' . mysqli_real_escape_string($conn, $table));

            return true;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to check if table exists: ' . $e);

            return false;
        }
    }

    /**
     * Does a record exist in the database?
     *
     * @param string $table Table name
     * @param string $search The term you want to search for (id)
     * @param string $term What the value should be (1)
     */
    public static function doesRecordExist(string $table, string $search, string $term): bool
    {
        try {
            if (self::doesTableExist($table) === false) {
                return false;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ? WHERE ? = ?;');
            $stmt->bind_param('sss', $table, $search, $term);
            $stmt->execute();
            $stmt->close();

            return true;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, 'Failed to check if record exists: ' . $e);

            return false;
        }
    }

    /**
     * Migrate the database.
     */
    public static function migrate(bool $isCli = false): void
    {
        try {
            $mysql = new MySQL();
            $db = $mysql->connectPDO();

            $db->exec('
            CREATE TABLE IF NOT EXISTS framework_migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                script VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');

            $sqlFiles = glob(__DIR__ . '/../../storage/migrate/database/*.sql');

            if (count($sqlFiles) > 0) {
                usort($sqlFiles, function ($a, $b) {
                    $aDate = intval(basename($a, '.sql'));
                    $bDate = intval(basename($b, '.sql'));

                    return $aDate - $bDate;
                });

                foreach ($sqlFiles as $sqlFile) {
                    $script = file_get_contents($sqlFile);

                    $fileName = basename($sqlFile); // Get only the file name

                    $stmt = $db->prepare('SELECT COUNT(*) FROM framework_migrations WHERE script = ?');
                    $stmt->execute([$fileName]);
                    $count = $stmt->fetchColumn();

                    if ($count == 0) {
                        $db->exec($script);

                        $stmt = $db->prepare('INSERT INTO framework_migrations (script) VALUES (?)');
                        $stmt->execute([$fileName]);
                        if ($isCli == true) {
                            echo Kernel::translateColorsCode('&rExecuted migration: &e' . $fileName . '&o');
                            echo Kernel::NewLine();
                        }
                    } else {
                        if ($isCli == true) {
                            echo Kernel::translateColorsCode('&rSkipping migration: &e' . $fileName . ' &r(&ealready executed&r)&o');
                            echo Kernel::NewLine();
                        }
                    }
                }
            } else {
                if ($isCli == true) {
                    echo Kernel::translateColorsCode('&cNo migrations found!&o');
                    echo Kernel::NewLine();
                } else {
                    throw new \Exception('No migrations found!');
                }
            }
        } catch (\Exception $e) {
            if ($isCli == true) {
                echo Kernel::translateColorsCode('&cFailed to migrate the database: ' . $e->getMessage() . '&o');
                echo Kernel::NewLine();
            } else {
                throw new \Exception('Failed to migrate the database: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get the date that can be used in MySQL.
     */
    public static function getDateLikeMySQL(): string
    {
        return date('Y-m-d H:i:s');
    }
}
