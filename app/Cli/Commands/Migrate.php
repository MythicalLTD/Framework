<?php

namespace MythicalSystemsFramework\Cli\Commands;

use Exception;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\Settings;

class Migrate extends Command
{
    public static string $description = 'A command that can help if you want to migrate the database or the config!';

    public static function execute(bool $isFrameworkCommand = false): void
    {
        echo self::log_info("");
        echo self::log_info("&c1.&7 Migrate the database");
        echo self::log_info("&c2.&7 Migrate the config");
        echo self::log_info("&c3.&7 Exit");
        echo self::log_info("");

        $option = readline("Select an option: ");

        switch ($option) {
            case '1':
                self::db();
                break;
            case '2':
                echo self::translateColorsCode("&cThis option is not available yet!&o");
                break;
            case '3':
                self::exit();
                break;
            default:
                echo "Invalid option selected.";
                break;
        }
    }


    public static function db(): void
    {
        try {
            self::log_info("Migrating the database...");
            self::log_info("");
            MySQL::migrate(true);
            Settings::migrate(true);
            self::log_success("&rDatabase migrated &asuccessfully&r!");
            self::exit();
        } catch (Exception $e) {
            die("Failed to migrate the database: " . $e->getMessage() . "");
        }
    }

    public static function cfg(): void
    {

    }
}
