<?php

namespace MythicalSystemsFramework\Cli;

class Kernel extends Colors
{
    /**
     * Executes the framework command with the specified command name.
     *
     * @param string $commandName the name of the command to execute
     *
     * @throws \Exception if the command file cannot be found, the command class does not exist,
     *                    or the command class does not have 'name' or 'description' properties
     */
    public static function executeFrameworkCommand(string $commandName): void
    {
        $commandName = ucfirst($commandName);
        $commandFile = __DIR__ . "/Commands/$commandName.php";

        if (!file_exists($commandFile)) {
            throw new \Exception('Command not found!');
        }

        require_once $commandFile;

        $commandClass = "MythicalSystemsFramework\\Cli\\Commands\\$commandName";

        if (!class_exists($commandClass)) {
            throw new \Exception('Command not found!');
        }

        $reflectionClass = new \ReflectionClass($commandClass);

        if (!$reflectionClass->hasProperty('description')) {
            throw new \Exception("Command class '$commandClass' does not have 'name' or 'description' properties.");
        }

        $commandClass::execute(true);
    }

    /**
     * Exit the CLI application.
     *
     * @param string $message the message to display before exiting
     */
    public static function exit(string $message = '&7Application exited &asuccessfully&7!'): void
    {
        if ($message !== '') {
            echo self::translateColorsCode($message);
        }

        exit;
    }
}
