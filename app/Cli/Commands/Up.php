<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

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
