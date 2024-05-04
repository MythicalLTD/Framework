<?php
namespace MythicalSystemsFramework\Kernel;

use Exception;

class Logger
{
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
     * Log something inside the kernel logs
     * 
     * @param string $level (INFO,WARNING,ERROR,CRITICAL,OTHER)
     * @param string $type (CORE,DATABASE,PLUGIN,LOG,OTHER)
     * @param string $message The message you want to log
     * 
     * @return int The log id!
     */
    public static function log(string $level, string $type, string $message): int
    {
        if (empty($level)) {
            throw new Exception("At least one log level must be provided");
        }
        if (empty($type)) {
            throw new Exception("At least one log type must be provided");
        }

        if (!$level == self::INFO || !$level == self::WARNING || !$level == self::ERROR || !$level == self::CRITICAL || !$level == self::OTHER) {
            throw new Exception("It looks like you did not provide a valid log level!");
        }

        if (!$type == self::CORE || !$type == self::DATABASE || !$type == self::PLUGIN || !$type == self::LOG || !$type == self::OTHER || !$type == self::LANGUAGE) {
            throw new Exception("It looks like you did not provide a valid type!");
        }
        $output = "[" . date("Y-m-d H:i:s") . "] (" . $type . '/' . $level . ") " . $message . "";

        $logs = self::getLogs();
        $logId = count($logs) + 1;

        $log = [
            'id' => $logId,
            'type' => $type,
            'level' => $level,
            'date' => date('Y-m-d H:i'),
            'message' => $message,
            'formatted' => $output,
        ];
        $logs[] = $log;

        self::saveLogs($logs);

        return $logId;
    }

     /**
     * Get the current logs from the JSON file.
     *
     * @return array
     */
    private static function getLogs(): array
    {
        $file = '../caches/logs.json';
        if (file_exists($file)) {
            $data = file_get_contents($file);
            return json_decode($data, true);
        } else {
            return [];
        }
    }

    /**
     * Save the logs to the JSON file.
     *
     * @param array $logs
     *
     * @return void
     */
    private static function saveLogs(array $logs): void
    {
        $file = '../caches/logs.json';
        file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT));
    }

    /**
     * Delete an log by ID.
     *
     * @param int $id
     * @return void
     */
    public static function delete(int $id): void
    {
        $logs = self::getLogs();

        $logs = array_filter($logs, function ($log) use ($id) {
            return $log['id'] !== $id;
        });

        self::saveLogs(array_values($logs));
    }

    /**
     * Delete all logs.
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        self::saveLogs([]);
    }

    /**
     * Get a single log by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getOne(int $id): ?array
    {
        $logs = self::getLogs();

        foreach ($logs as $log) {
            if ($log['id'] === $id) {
                return $log;
            }
        }

        return null;
    }

    /**
     * Get all logs.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return self::getLogs();
    }

    /**
     * Get all logs sorted by ID in descending order.
     *
     * @return array
     */
    public static function getAllSortedById(): array
    {
        $logs = self::getLogs();

        usort($logs, function ($a, $b) {
            return $b['id'] - $a['id'];
        });

        return $logs;
    }

    /**
     * Get all logs sorted by date in descending order.
     *
     * @return array
     */
    public static function getAllSortedByDate(): array
    {
        $logs = self::getLogs();

        usort($logs, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $logs;
    }
}