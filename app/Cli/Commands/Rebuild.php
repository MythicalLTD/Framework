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

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Encryption\XChaCha20;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Rebuild extends Command implements CommandBuilder
{
    public static string $description = 'A command that can help if you want to rebuild the app!';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('');
        echo self::log_info('&c1.&7 Rebuild the database');
        echo self::log_info('&c2.&7 Rebuild the app');
        echo self::log_info('&c3.&7 Exit');
        echo self::log_info('');

        $option = readline('Select an option: ');

        switch ($option) {
            case '1':
                self::db();
                break;
            case '2':
                self::app();
                break;
            case '3':
                self::exit();
                break;
            default:
                echo 'Invalid option selected.';
                break;
        }
    }

    public static function db(): void
    {
        try {
            $db = new MySQL();
            if ($db->tryConnection(cfg::get('database', 'host'), cfg::get('database', 'port'), cfg::get('database', 'username'), cfg::get('database', 'password'), cfg::get('database', 'name')) == true) {
                echo self::translateColorsCode('&rConnection to the database was &asuccessful!');

                echo self::translateColorsCode('&4&lWARNING: &rThis option will wipe the database. ');
                echo self::translateColorsCode('&4&lWARNING: &rOnly use this function if you know what you are doing ');
                echo self::translateColorsCode('&4&lWARNING: &rOnce you wipe the database there is no going back! ');
                echo self::translateColorsCode("&4&lWARNING: &rPlease be careful and don't play around with commands!  ");
                echo self::translateColorsCode('&4&lWARNING: &rThere is no other message then this so keep in mind! ');
                echo self::translateColorsCode('&4&lWARNING: &rDo you really want to wipe the database? (&ey&r/&en&r): ');

                $confirm = readline();
                if (strtolower($confirm) == 'y') {
                    try {
                        $mysql = new MySQL();
                        $db = $mysql->connectPDO();

                        $db->exec('SET FOREIGN_KEY_CHECKS = 0');
                        $tables = $db->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
                        foreach ($tables as $table) {
                            $db->exec("DROP TABLE IF EXISTS $table");
                        }
                        $db->exec('SET FOREIGN_KEY_CHECKS = 1');

                        echo self::translateColorsCode('&rDatabase wiped!!');
                        MySQL::migrate(true);
                        Settings::migrate(true);

                        echo self::translateColorsCode('&rDatabase rebuilt!');
                        echo self::translateColorsCode('&rGenerating an encryption key for database...!');
                        echo self::translateColorsCode('&rPlease wait...');
                        echo self::translateColorsCode('&rWe generated a key for you: &e' . XChaCha20::generateKey() . '');
                        echo self::translateColorsCode('&rKey generated &asuccessfully&r!');
                        echo self::translateColorsCode("&rLet's start by setting up your configuration!");
                        Configure::configure();
                    } catch (\PDOException $e) {
                        echo self::translateColorsCode('&rFailed to drop tables: &c' . $e->getMessage() . '');

                    }
                } else {
                    self::exit('&rExiting...');
                }
            } else {
                self::exit('&cFailed to connect to the database!');
            }
        } catch (\Exception $e) {
            self::exit('&cFailed to rebuild the database: &r' . $e->getMessage() . '');
        }
    }

    public static function app(): void
    {
        try {
            self::log_info('Rebuilding the app...');
            self::log_info('');
            self::log_success('&rApp rebuilt &asuccessfully&r!');
            self::exit();
        } catch (\Exception $e) {
            exit('Failed to rebuild the app: ' . $e->getMessage() . '');
        }
    }
}
