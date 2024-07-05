<?php

namespace MythicalSystemsFramework\Kernel;

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;

class ErrorHandler
{
    /**
     * Displays a Server Error Message
     * 
     * @param string $error_name The title of the error
     * @param string $php_error The error from php 
     * @param string $file The file path from where the error comes
     * @param string $code The code line from where the error comes from
     * 
     * @return void 
     */
    public static function ServerError(string $error_name, string $php_error, string $file, string $code): void
    {
        /**
         * TODO: Implement a better error handling system
         * 
         * if (cfg::get('framework', 'debug') == "true") {
         *     $template = file_get_contents(__DIR__ . '/../../core/templates/critical.html');
         *     $placeholders = array('%PHP_ERROR_NAME%', '%PHP_ERROR%', '%ERROR_FILE_NAME%', '%CODE_LINE%', '%LAST_SQL%');
         *     $values = array($error_name, $php_error, $file, $code); 
         *     $lastSQLCommands = [];
         *     for ($i = 1; $i <= 15; $i++) {
         *         $sqlCommand = cache::get('SQL_' . $i);
         *         if ($sqlCommand !== null) {
         *             $lastSQLCommands[] = $sqlCommand;
         *         }
         *     } 
         *     $values[4] = implode("\n", $lastSQLCommands);
         *     $view = str_replace($placeholders, $values, $template);
         *     die($view);
         * } else {
         *     $template = file_get_contents(__DIR__ . '/../../core/templates/500.html');
         *     die($template);
         * }
         */
    }
}
