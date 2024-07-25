<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\Kernel;

class Command extends Kernel
{
    public static string $description = 'A example command :)';

    public static function execute(bool $isFrameworkCommand = false): void
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
}
