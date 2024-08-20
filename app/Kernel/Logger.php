<?php

namespace MythicalSystemsFramework\Kernel;

use MythicalSystemsFramework\Database\MySQL;

class Logger
{
    /**
     * Log something inside the kernel framework_logs.
     *
     * @param LoggerTypes|string $level (INFO, WARNING, ERROR, CRITICAL, OTHER)
     * @param LoggerLevels|string $type (CORE, DATABASE, PLUGIN, LOG, OTHER, LANGUAGE, BACKUP)
     * @param string $message The message you want to log
     *
     * @return int The log id!
     */
    public static function log(LoggerTypes|string $level, LoggerLevels|string $type, string $message): int
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        if (empty($level) || empty($type)) {
            throw new \Exception('Both log level and type must be provided');
        }

        $output = '[' . date('Y-m-d H:i:s') . '] (' . $type . '/' . $level . ') ' . $message . '';

        $stmt = $conn->prepare('INSERT INTO framework_logs (l_type, levels, message, formatted) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $type, $level, $message, $output);
        $stmt->execute();
        $logId = $stmt->insert_id;
        $stmt->close();

        return $logId;
    }

    /**
     * Delete a log by ID.
     */
    public static function delete(int $id): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $stmt = $conn->prepare('DELETE FROM framework_logs WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Delete all framework_logs.
     */
    public static function deleteAll(): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $conn->query('TRUNCATE TABLE framework_logs');
    }

    /**
     * Get a single log by ID.
     */
    public static function getOne(int $id): ?array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $stmt = $conn->prepare('SELECT * FROM framework_logs WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $log = $result->fetch_assoc();
        $stmt->close();

        return $log ? $log : null;
    }

    /**
     * Get all framework_logs.
     */
    public static function getAll(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $result = $conn->query('SELECT * FROM framework_logs');
        $framework_logs = $result->fetch_all(MYSQLI_ASSOC);

        return $framework_logs;
    }

    /**
     * Get all framework_logs sorted by ID in descending order.
     */
    public static function getAllSortedById(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $result = $conn->query('SELECT * FROM framework_logs ORDER BY id DESC');
        $framework_logs = $result->fetch_all(MYSQLI_ASSOC);

        return $framework_logs;
    }

    /**
     * Get all framework_logs sorted by date in descending order.
     *
     * @param LoggerTypes|string $level (INFO, WARNING, ERROR, CRITICAL, OTHER)
     * @param LoggerLevels|string $type (CORE, DATABASE, PLUGIN, LOG, OTHER, LANGUAGE,BACKUP)
     * @param int $limit The amount of logs you want to get (15 by default)
     *
     * @return array|null Returns the logs in an array
     */
    public static function getAllSortedByDate(LoggerTypes|string $level, LoggerLevels|string $type, int $limit = 15): ?array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $query = 'SELECT * FROM framework_logs WHERE levels = ? AND l_type = ? ORDER BY date DESC LIMIT ?';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $level, $type, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $framework_logs = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $framework_logs;
    }
}
