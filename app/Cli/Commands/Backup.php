<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;

class Backup extends Command implements CommandBuilder
{
    public static string $description = 'A command to backup the application.';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('Backing up the application...');
    }
}
