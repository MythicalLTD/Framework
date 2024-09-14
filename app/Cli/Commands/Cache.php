<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Database\MySQLCache;
use MythicalSystemsFramework\Cache\Cache as CacheWorker;

class Cache extends Command implements CommandBuilder
{
    public static string $description = 'A command that can help if you want to cache the database or the config!';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('');
        echo self::log_info('&c1.&7 Flush the caches!');
        echo self::log_info('&c2.&7 Process the caches!');
        echo self::log_info('&c3.&7 Create a table cache!');
        echo self::log_info('&c4.&7 Exit');
        echo self::log_info('');

        $option = readline('Select an option: ');

        switch ($option) {
            case '1':
                self::flush();
                break;
            case '2':
                self::process();
                break;
            case '3':
                self::create();
                break;
            case '4':
                self::exit();
                break;
            default:
                echo 'Invalid option selected.';
                break;
        }
    }

    public static function flush(): void
    {
        echo self::translateColorsCode('Please wait while we purge your &ccaches&r.&o');
        try {
            CacheWorker::purge();
            echo self::translateColorsCode('&rPurged caches!');
        } catch (\Exception $e) {
            echo self::translateColorsCode('&cFailed to purge caches: &r' . $e->getMessage());
        }
    }

    public static function process(): void
    {
        echo self::translateColorsCode('Please wait while we process your &ccaches&r.&o');
        try {
            CacheWorker::process();
            echo self::translateColorsCode('&rProcessed caches!');
        } catch (\Exception $e) {
            echo self::translateColorsCode('&cFailed to process caches: &r' . $e->getMessage());
        }
    }

    public static function create(): void
    {
        echo self::translateColorsCode('Please wait while we create your &ccaches&r.&o');
        $table = readline('Enter the name of the table you want to cache: ');
        $status = MySQLCache::saveCache($table);
        switch ($status) {
            case 'ERROR_TABLE_DOES_NOT_EXIST':
                self::exit('&cTable not found!&o');
                break;
            case 'ERROR_NO_DATA_FOUND_IN_TABLE':
                self::exit('&cNo data found in table!&o');
                break;
            case 'ERROR_TABLE_NOT_SUPPORTED':
                self::exit('&cTable does not support cache!&o');
                break;
            case 'ERROR_MYSQL_ERROR':
                self::exit('&cThere was an error while trying to connect to the mysql server :(&o');
                break;
            case 'OK':
                self::exit('&rCreated &acaches&r!&o');
                break;
            default:
                break;
        }
    }
}
