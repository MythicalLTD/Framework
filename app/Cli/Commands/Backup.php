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

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Backup\Database;
use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Backup\Backup as BackupUtil;

class Backup extends Command implements CommandBuilder
{
    public static string $description = 'A command to backup the application.';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('');
        echo self::log_info('&c1.&7 Create a backup');
        echo self::log_info('&c2.&7 List backups.');
        echo self::log_info('&c3.&7 Restore a backup.');
        echo self::log_info('&c4.&7 Delete backup.');
        echo self::log_info('&c5.&7 Exit');
        echo self::log_info('');

        $option = readline('Select an option: ');

        switch ($option) {
            case '1':
                self::create();
                break;
            case '2':
                self::list();
                break;
            case '3':
                self::restore();
                break;
            case '4':
                self::delete();
                break;
            case '5':
                self::exit();
                break;
            default:
                echo self::log_info('&7Invalid option selected. ');
                break;
        }
    }

    public static function create(): void
    {
        $backup = BackupUtil::take();
        if ($backup > 0) {
            BackupUtil::setBackupStatus($backup, \MythicalSystemsFramework\Backup\Status::DONE);
            echo self::log_info('Backup created successfully!');
        } else {
            BackupUtil::setBackupStatus($backup, \MythicalSystemsFramework\Backup\Status::FAILED);
            echo self::log_info('Failed to create backup!');
        }
    }

    public static function restore(): void
    {
        self::list();
        echo self::translateColorsCode('Enter the backup ID you want to restore: ');
        $backup_id = (int) readline('');
        if ($backup_id <= 0) {
            echo self::log_info('Invalid backup ID!');

            return;
        }
        if (empty($backup_id)) {
            echo self::log_info('Backup ID cannot be empty!');

            return;
        }

        if (Database::doesBackupExist($backup_id)) {
            echo self::log_info('Backup exists!');

            echo self::translateColorsCode('&4&lWARNING: &rThis option will wipe the database. ');
            echo self::translateColorsCode('&4&lWARNING: &rOnly use this function if you know what you are doing ');
            echo self::translateColorsCode('&4&lWARNING: &rOnce you wipe the database there is no going back! ');
            echo self::translateColorsCode("&4&lWARNING: &rPlease be careful and don't play around with commands!  ");
            echo self::translateColorsCode('&4&lWARNING: &rThere is no other message then this so keep in mind! ');
            echo self::translateColorsCode('&4&lWARNING: &rDo you really want to wipe the database? (&ey&r/&en&r): ');

            $confirm = readline();
            if (strtolower($confirm) == 'y') {
                BackupUtil::restore($backup_id);
                self::create();
            } else {
                echo self::log_info('Restore cancelled!');
            }
        } else {
            echo self::log_info('Backup does not exist!');
        }
    }

    public static function delete(): void
    {
        self::list();
        echo self::translateColorsCode('Enter the backup ID you want to delete: ');
        $backup_id = (int) readline('');
        if ($backup_id <= 0) {
            echo self::log_info('Invalid backup ID!');

            return;
        }
        if (empty($backup_id)) {
            echo self::log_info('Backup ID cannot be empty!');

            return;
        }

        if (Database::doesBackupExist($backup_id)) {
            BackupUtil::remove($backup_id);
        } else {
            echo self::log_info('Backup does not exist!');
        }
    }

    public static function list(): void
    {
        $backups = BackupUtil::getBackups();
        foreach ($backups as $backup) {

            echo self::log_info('------ &7[&cBackup Info&7] ------');

            echo self::log_info('Backup ID: &c' . $backup['id']);
            echo self::log_info('Backup Status: &c' . $backup['backup_status']);
            echo self::log_info('Backup Date: &c' . $backup['backup_date_end']);

            echo self::log_info('------ (&c' . $backup['id'] . '&7/&c' . count($backups) . '&7) ------');
        }
    }
}
