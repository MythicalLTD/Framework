<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;

class Help extends Command implements CommandBuilder
{
    public static string $description = 'The help command to get info about other commands!';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('');
        echo self::log_info('-----------------------');
        echo self::log_info('| Available commands: |');
        echo self::log_info('-----------------------');
        echo self::log_info('');

        $commands = scandir(__DIR__);
        foreach ($commands as $command) {
            if ($command === '.' || $command === '..' || $command === 'Command.php') {
                continue;
            }

            $command = str_replace('.php', '', $command);
            $commandClass = "MythicalSystemsFramework\\Cli\\Commands\\$command";
            $reflectionClass = new \ReflectionClass($commandClass);

            if (!$reflectionClass->hasProperty('description')) {
                continue;
            }

            echo self::log_info("{$command} > {$reflectionClass->getProperty('description')->getValue()}");
        }
        echo self::log_info('');
    }
}
