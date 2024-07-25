<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Configure extends Command
{
    public static string $description = 'A command that can help if you want to configure the app!';

    public static function execute(bool $isFrameworkCommand = false): void
    {
        echo self::log_info('');
        echo self::log_info('&c1.&7 Configure the database');
        echo self::log_info('&c2.&7 Configure the app');
        echo self::log_info('&c3.&7 Exit');
        echo self::log_info('');

        $option = readline('Select an option: ');

        switch ($option) {
            case '1':
                self::dbconfigure();
                break;
            case '2':
                self::configure();
                break;
            case '3':
                self::exit();
                break;
            default:
                echo 'Invalid option selected.';
                break;
        }
    }

    public static function dbconfigure(): void
    {
        if (cfg::get('database', 'password') !== null) {
            echo self::translateColorsCode('&rDatabase configuration already exists. &oWould you like to overwrite it? &8[&aY&8/&cN&8]&r: ');
            $overwrite = readline();
            if (strtolower($overwrite) !== 'y') {
                return;
            }
        }
        $defaultHost = '127.0.0.1';
        $defaultPort = '3306';
        $db = new MySQL();
        echo self::translateColorsCode("&rEnter the host of the database &8[&e$defaultHost&8]&r: ");
        $host = readline() ?: $defaultHost;
        echo self::translateColorsCode("&rEnter the port of the database &8[&e$defaultPort&8]&r: ");
        $port = readline() ?: $defaultPort;
        echo self::translateColorsCode('&rEnter the username: ');
        $username = readline();
        echo self::translateColorsCode('&rEnter the password: ');
        $password = readline();
        echo self::translateColorsCode('&rEnter the database name: ');
        $database = readline();
        // Perform validation
        if (empty($username) || empty($password) || empty($database)) {
            echo self::translateColorsCode('&cPlease provide all the required information.&o');

            return;
        }

        // Hide the password
        $hiddenPassword = str_repeat('*', strlen($password));

        // Use the provided information
        echo self::NewLine();
        echo self::translateColorsCode("&rHost: &e$host&o");
        echo self::translateColorsCode("&rPort: &e$port&o");
        echo self::translateColorsCode("&rUsername: &e$username&o");
        echo self::translateColorsCode("&rPassword: &e$hiddenPassword&o");
        echo self::translateColorsCode("&rDatabase: &e$database&o");

        if ($db->tryConnection($host, $port, $username, $password, $database) == true) {
            echo self::NewLine();
            echo self::translateColorsCode('&rConnection to the database was &asuccessful!&o');
            echo self::NewLine();
            echo self::translateColorsCode('&rSaving the configuration...&o');
            cfg::set('database', 'host', $host);
            cfg::set('database', 'port', $port);
            cfg::set('database', 'username', $username);
            cfg::set('database', 'password', $password);
            cfg::set('database', 'name', $database);
            echo self::translateColorsCode('&rGenerating an encryption key for database...!&o');
            $key = 'mythicalcore_' . bin2hex(random_bytes(64 * 128));
            cfg::set('encryption', 'key', $key);
            echo self::translateColorsCode('&rKey generated &asuccessfully&r!&o');
            echo self::translateColorsCode('&rConfiguration saved &asuccessfully&r!&o');
        } else {
            echo self::translateColorsCode('&7Failed to connect to the database. &o&rPlease check the provided information.');
        }
    }

    public static function configure(): void
    {
        self::exit('This feature is not implemented yet.');
    }
}
