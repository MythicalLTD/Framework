<?php
namespace MythicalSystemsFramework\Cli;

use Exception;

class Kernel
{
    /**
     * Executes the command specified by the command name.
     *
     * @param string $commandName The name of the command to execute.
     * 
     * @return void
     * @throws Exception If the command file cannot be found or the command class does not exist.
     */
    public static function executeCommand($commandName): void
    {
        $commandFile = self::findCommandFile($commandName);
        if ($commandFile !== null) {
            require_once $commandFile;
            $commandClass = ucfirst($commandName) . 'Command';
            if (class_exists($commandClass)) {
                $command = new $commandClass();
                $command->execute();
            } else {
                throw new Exception("Command class not found: $commandClass");
            }
        } else {
            throw new Exception("Command file not found: $commandName");
        }
    }

    /**
     * Recursively searches for the command file with the specified command name.
     *
     * @param string $commandName The name of the command to search for.
     * @param string $directory The directory to search in. Defaults to '/var/www/network-masters/framework/commands'.
     * 
     * @return string|null The path to the command file if found, or null if not found.
     */
    public static function findCommandFile($commandName, $directory = __DIR__ . '/../../commands'): string|null
    {
        $files = scandir($directory);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $directory . '/' . $file;
            if (is_dir($path)) {
                $subCommandFile = self::findCommandFile($commandName, $path);
                if ($subCommandFile !== null) {
                    return $subCommandFile;
                }
            } elseif (pathinfo($file, PATHINFO_FILENAME) === $commandName) {
                return $path;
            }
        }
        return null;
    }
}