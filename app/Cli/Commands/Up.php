<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Up extends Command implements CommandBuilder
{
    public static string $description = 'Mark the application as up.';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('&rMarking the application as up...');
        if (cfg::get('app', 'maintenance') == 'false'  && cfg::get('app', 'maintenance') != 'true') {
            echo self::translateColorsCode('&aApplication &ris already up!');

            return;
        }
        cfg::set('app', 'maintenance', 'false');
        echo self::log_success('Application is now &aup&r!');
    }
}
