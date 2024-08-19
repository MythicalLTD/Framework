<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;

class Update extends Command implements CommandBuilder
{
    public static string $description = 'Update the application.';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('Updating the application...');
        echo self::log_error('This command is not yet implemented!');
    }
}
