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
use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Encryption\XChaCha20;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Configure extends Command implements CommandBuilder
{
    public static string $description = 'A command that can help if you want to configure the app!';

    public static function execute(bool $isFrameworkCommand, array $args): void
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
            echo self::translateColorsCode('&rDatabase configuration already exists. Would you like to overwrite it? &8[&aY&8/&cN&8]&r: ');
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
            echo self::translateColorsCode('&cPlease provide all the required information.');

            return;
        }

        // Hide the password
        $hiddenPassword = str_repeat('*', strlen($password));

        // Use the provided information

        echo self::translateColorsCode("&rHost: &e$host");
        echo self::translateColorsCode("&rPort: &e$port");
        echo self::translateColorsCode("&rUsername: &e$username");
        echo self::translateColorsCode("&rPassword: &e$hiddenPassword");
        echo self::translateColorsCode("&rDatabase: &e$database");

        if ($db->tryConnection($host, $port, $username, $password, $database) == true) {

            echo self::translateColorsCode('&rConnection to the database was &asuccessful!');

            echo self::translateColorsCode('&rSaving the configuration...');
            cfg::set('database', 'host', $host);
            cfg::set('database', 'port', $port);
            cfg::set('database', 'username', $username);
            cfg::set('database', 'password', $password);
            cfg::set('database', 'name', $database);
            echo self::translateColorsCode('&rGenerating an encryption key for database...!');
            echo self::translateColorsCode('&rPlease wait...');
            echo self::translateColorsCode('&rWe generated a key for you: &e' . XChaCha20::generateKey() . '');
            echo self::translateColorsCode('&rKey generated &asuccessfully&r!');
            echo self::translateColorsCode('&rConfiguration saved &asuccessfully&r!');
        } else {
            echo self::translateColorsCode('&7Failed to connect to the database. &rPlease check the provided information.');
        }
    }

    public static function configure(): void
    {
        echo self::translateColorsCode('Enter the name of the application &8[&eMythicalFramework&8]&r: ');
        $app_name = readline() ?: 'MythicalFramework';
        echo self::translateColorsCode('Enter the logo of the application &8[&ehttps://avatars.githubusercontent.com/u/117385445&8]&r: ');
        $app_logo = readline() ?: 'https://avatars.githubusercontent.com/u/117385445';
        echo self::translateColorsCode('Enter the timezone of the application &8[&eEurope/Vienna&8]&r: ');
        $app_timezone = readline() ?: 'Europe/Vienna';

        // SAVE IT SOMEWHERE I THINK:::
    }
}
