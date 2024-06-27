<?php

namespace MythicalSystemsFramework\Kernel;

use Exception;
use MythicalSystemsFramework\Database\MySQL;

class Logger
{
    // Log level 
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';
    // Log types
    const CORE = 'CORE';
    const DATABASE = 'DATABASE';
    const PLUGIN = 'PLUGIN';
    const LOG = 'LOG';
    const LANGUAGE = "LANGUAGE";
    // Other
    const OTHER = 'OTHER';

    /**
     * Log something inside the kernel framework_logs
     * 
     * @param string $level (INFO, WARNING, ERROR, CRITICAL, OTHER)
     * @param string $type (CORE, DATABASE, PLUGIN, LOG, OTHER)
     * @param string $message The message you want to log
     * 
     * @return int The log id!
     */
    public static function log(string $level, string $type, string $message): int
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        if (empty($level) || empty($type)) {
            throw new Exception("Both log level and type must be provided");
        }

        $output = "[" . date("Y-m-d H:i:s") . "] (" . $type . '/' . $level . ") " . $message . "";

        $stmt = $conn->prepare("INSERT INTO framework_logs (type, levels, message, formatted, date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $type, $level, $message, $output);
        $stmt->execute();
        $logId = $stmt->insert_id;
        $stmt->close();

        return $logId;
    }

    /**
     * Delete a log by ID.
     *
     * @param int $id
     * @return void
     */
    public static function delete(int $id): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $stmt = $conn->prepare("DELETE FROM framework_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Delete all framework_logs.
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $conn->query("TRUNCATE TABLE framework_logs");
    }

    /**
     * Get a single log by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getOne(int $id): ?array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $stmt = $conn->prepare("SELECT * FROM framework_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $log = $result->fetch_assoc();
        $stmt->close();
        return $log ? $log : null;
    }

    /**
     * Get all framework_logs.
     *
     * @return array
     */
    public static function getAll(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $result = $conn->query("SELECT * FROM framework_logs");
        $framework_logs = $result->fetch_all(MYSQLI_ASSOC);
        return $framework_logs;
    }

    /**
     * Get all framework_logs sorted by ID in descending order.
     *
     * @return array
     */
    public static function getAllSortedById(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $result = $conn->query("SELECT * FROM framework_logs ORDER BY id DESC");
        $framework_logs = $result->fetch_all(MYSQLI_ASSOC);
        return $framework_logs;
    }

    /**
     * Get all framework_logs sorted by date in descending order.
     *
     * @return array
     */
    public static function getAllSortedByDate(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $result = $conn->query("SELECT * FROM framework_logs ORDER BY date DESC");
        $framework_logs = $result->fetch_all(MYSQLI_ASSOC);
        return $framework_logs;
    }
}
