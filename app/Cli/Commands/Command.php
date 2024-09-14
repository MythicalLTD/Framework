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

use MythicalSystemsFramework\Cli\Kernel;
use MythicalSystemsFramework\Cli\CommandBuilder;

class Command extends Kernel implements CommandBuilder
{
    public static string $description = 'A example command :)';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        /*
         * This method should be overridden in the child class.
         */
        echo self::translateColorsCode('Command executed &asuccessfully&r!');
    }

    /**
     * Logs a success message to the console.
     *
     * @requires \MythicalSystemsFramework\Cli\Commands\Colors
     *
     * @param string $message the message to log
     */
    public static function log_success(string $message): void
    {
        echo self::translateColorsCode("&a$message&r&o");
    }

    /**
     * Logs a error message to the console.
     *
     * @requires \MythicalSystemsFramework\Cli\Commands\Colors
     *
     * @param string $message the message to log
     */
    public static function log_error(string $message): void
    {
        echo self::translateColorsCode("&c$message&r&o");
    }

    /**
     * Logs a warning message to the console.
     *
     * @requires \MythicalSystemsFramework\Cli\Commands\Colors
     *
     * @param string $message the message to log
     */
    public static function log_warning(string $message): void
    {
        echo self::translateColorsCode("&e$message&r&o");
    }

    /**
     * Logs a info message to the console.
     *
     * @requires \MythicalSystemsFramework\Cli\Commands\Colors
     *
     * @param string $message the message to log
     */
    public static function log_info(string $message): void
    {
        echo self::translateColorsCode("&7$message&r&o");
    }

    /**
     * Arguments start from 1 including one.
     *
     * @return mixed
     */
    public static function getArgument(array $args, int $index): ?string
    {
        try {
            return $args[$index + 1];
        } catch (\Exception $e) {
            return null;
        }
    }
}
