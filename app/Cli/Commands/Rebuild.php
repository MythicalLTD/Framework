<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Rebuild extends Command
{
    public static string $description = 'A command that can help if you want to rebuild the app!';

    public static function execute(bool $isFrameworkCommand = false): void
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
                echo self::translateColorsCode('&o&fConnection to the database was &asuccessful!&o');
                echo self::NewLine();
                echo self::translateColorsCode('&4&lWARNING: &fThis option will wipe the database. &o');
                echo self::translateColorsCode('&4&lWARNING: &fOnly use this function if you know what you are doing &o');
                echo self::translateColorsCode('&4&lWARNING: &fOnce you wipe the database there is no going back! &o');
                echo self::translateColorsCode("&4&lWARNING: &fPlease be careful and don't play around with commands!  &o");
                echo self::translateColorsCode('&4&lWARNING: &fThere is no other message then this so keep in mind! &o');
                echo self::translateColorsCode('&4&lWARNING: &fDo you really want to wipe the database? (&ey&f/&en&f): ');

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
                        echo self::NewLine();
                        echo self::NewLine();
                        echo self::NewLine();
                        echo self::NewLine();
                        echo self::NewLine();
                        echo self::translateColorsCode('&fDatabase wiped!!&o');
                        MySQL::migrate(true);
                        Settings::migrate(true);
                        echo self::NewLine();
                        echo self::translateColorsCode('&fDatabase rebuilt!&o');
                        echo self::translateColorsCode("&fLet's start by setting up your configuration!&o");
                        Configure::configure();
                    } catch (\PDOException $e) {
                        echo self::translateColorsCode('&fFailed to drop tables: &c' . $e->getMessage() . '&o');
                        echo self::NewLine();
                    }
                } else {
                    self::exit('&fExiting...&o');
                }
            } else {
                self::exit('&cFailed to connect to the database!&o');
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
