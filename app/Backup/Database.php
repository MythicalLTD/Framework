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

namespace MythicalSystemsFramework\Backup;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class Database implements Status
{
    /**
     * Register that a backup is being taken!
     *
     * @return bool
     */
    public static function registerBackup(string $path): int
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('INSERT INTO framework_backups (backup_path) VALUES (?)');
            $stmt->bind_param('s', $path);
            $stmt->execute();
            Logger::log(LoggerLevels::INFO, LoggerTypes::BACKUP, 'A new backup has been started.');

            return $stmt->insert_id;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to register backup: ' . $e->getMessage());

            return 0;
        }
    }

    /**
     * Does a backup exist in the database?
     *
     * @param int $id The id of the backup
     *
     * @return bool Does the backup exist?
     */
    public static function doesBackupExist(int $id): bool
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_backups WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to check if backup exists: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Mark a backup as completed.
     *
     * @param int $id The id of the backup
     */
    public static function setBackupStatus(int $id, Status|string $status): void
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            if (self::doesBackupExist($id)) {
                if (MySQL::getLock('framework_backups', $id) == false) {
                    Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to mark backup as completed: Failed to get lock.');

                    return;
                }
                MySQL::requestLock('framework_backups', $id);
                $date = MySQL::getDateLikeMySQL();
                $stmt = $conn->prepare('UPDATE framework_backups SET backup_status = ?, backup_date_end = ? WHERE id = ?');
                $stmt->bind_param('ssi', $status, $date, $id);
                $stmt->execute();
                MySQL::requestUnLock('framework_backups', $id);
                Logger::log(LoggerLevels::INFO, LoggerTypes::BACKUP, 'Backup has been marked as completed.');
            } else {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to mark backup as completed: Backup does not exist.');
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to mark backup as completed: ' . $e->getMessage());
        }
    }

    /**
     * Get the status of a backup.
     */
    public static function getBackupStatus(int $id): string
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT backup_status FROM framework_backups WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $status = $result->fetch_assoc();

            return $status['backup_status'];
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to get backup status: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Get all backups from the database.
     */
    public static function getBackups(): array
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            $result = $conn->query("SELECT * FROM framework_backups WHERE `deleted` = 'false'");
            if ($result->num_rows == 0) {
                return [];
            }
            $backups = [];
            while ($row = $result->fetch_assoc()) {
                $backups[] = $row;
            }

            return $backups;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to get backups: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Mark a backup as deleted.
     */
    public static function markAsDeleted(int $id): void
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_backups WHERE `framework_backups`.`id` = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Backup/Database.php) Failed to mark backup as deleted: ' . $e->getMessage());
        }
    }

    /**
     * Get the backup path.
     *
     * @return array|bool|null
     */
    public static function getBackupPath(int $id)
    {
        try {
            $db = new MySQL();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT backup_path FROM framework_backups WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                return $row['backup_path'];
            }

            return null;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, 'Failed to get the backup path' . $e->getMessage());
        }
    }
}
