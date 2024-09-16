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

use Ifsnop\Mysqldump as IMysqldump;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Backup extends Database implements Status
{
    /**
     * Take a backup of the MySQL database.
     */
    public static function take(): int
    {
        global $event;
        try {
            $date = MySQL::getDateLikeMySQL();
            $date = str_replace(' ', '_', $date);
            $date = str_replace(':', '_', $date);
            $date = str_replace('-', '_', $date);
            $path = __DIR__ . '/../../storage/backups/database/backup_sql_' . $date . '.sql';

            $dump = new IMysqldump\Mysqldump('mysql:host=' . cfg::get('database', 'host') . ';dbname=' . cfg::get('database', 'name'), cfg::get('database', 'username'), cfg::get('database', 'password'));
            $dump->start($path);
            $id = self::registerBackup($path);
            if (!$id == 0) {
                $event->emit('backup.onBackupTaken', [$path, $id]);

                return $id;
            }

            return 0;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::BACKUP, '(App/Backup/Backup.php) Failed to take MySQL backup: ' . $e->getMessage());

            return 0;
        }
    }

    /**
     * Restore the backup from the database.
     */
    public static function restore(int $backup_id): void
    {
        global $event;
        try {
            if (!self::doesBackupExist($backup_id)) {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::BACKUP, '(App/Backup/Backup.php) Failed to restore MySQL backup: Backup does not exist.');

                return;
            }
            $path = self::getBackupPath($backup_id);

            $mysql = new MySQL();
            $db = $mysql->connectPDO();

            $db->exec('SET FOREIGN_KEY_CHECKS = 0');
            $tables = $db->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $db->exec("DROP TABLE IF EXISTS $table");
            }
            $db->exec('SET FOREIGN_KEY_CHECKS = 1');

            $db->exec('SET FOREIGN_KEY_CHECKS = 0');
            $sql = file_get_contents($path);
            $db->exec($sql);
            $db->exec('SET FOREIGN_KEY_CHECKS = 1');
            unlink($path);
            $event->emit('backup.onBackupRestore', [$path, $backup_id]);
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::BACKUP, '(App/Backup/Backup.php) Failed to restore MySQL backup: ' . $e->getMessage());
        }
    }

    /**
     * Remove a backup from the database / storage.
     */
    public static function remove(int $backup_id): void
    {
        global $event;
        try {
            if (!self::doesBackupExist($backup_id)) {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::BACKUP, '(App/Backup/Backup.php) Failed to remove MySQL backup: Backup does not exist.');

                return;
            }
            $path = self::getBackupPath($backup_id);
            unlink($path);
            self::markAsDeleted($backup_id);
            $event->emit('backup.onBackupDelete', [$path, $backup_id]);

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::BACKUP, '(App/Backup/Backup.php) Failed to remove MySQL backup: ' . $e->getMessage());
        }
    }
}
