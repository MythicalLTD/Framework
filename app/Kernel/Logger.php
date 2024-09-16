<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

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
        try {
            global $event; // This is a global variable that is used to emit events.
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
            $event->emit('logger.Log', [$level, $type, $message]);

            return $logId;
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Delete a log by ID.
     */
    public static function delete(int $id): void
    {
        try {
            global $event; // This is a global variable that is used to emit events.
            if (!self::doesLogExist($id)) {
                throw new \Exception('Log does not exist');
            }
            $event->emit('logger.Delete', [$id]);
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_logs WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Delete all framework_logs.
     */
    public static function deleteAll(): void
    {
        global $event; // This is a global variable that is used to emit events.
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $event->emit('logger.DeleteAll');
            $conn->query('TRUNCATE TABLE framework_logs');
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
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

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all framework_logs sorted by ID in descending order.
     */
    public static function getAllSortedById(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();
        $result = $conn->query('SELECT * FROM framework_logs ORDER BY id DESC');

        return $result->fetch_all(MYSQLI_ASSOC);
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

    /**
     * Does this log exist?
     */
    public static function doesLogExist(int $id): bool
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_logs WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $log = $result->fetch_assoc();
            $stmt->close();

            return $log ? true : false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
