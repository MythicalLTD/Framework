<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Down extends Command implements CommandBuilder
{
    public static string $description = 'Mark the application as down.';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('Marking the application as down...');
        if (cfg::get('app', 'maintenance') == 'true' && cfg::get('app', 'maintenance') != 'false') {
            echo self::translateColorsCode('&aApplication &ris already in maintenance mode!');

            return;
        }
        cfg::set('app', 'maintenance', 'true');
        echo self::log_success('Application is now down!');
    }
}
