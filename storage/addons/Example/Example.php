<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

use MythicalSystemsFramework\Plugins\PluginBuilder;

class Example implements PluginBuilder
{
    public function Main(): void
    {
        // TODO: Implement the main function
    }

    public function Event(MythicalSystemsFramework\Plugins\PluginEvent $eventHandler): void
    {
        $eventHandler->on('app.onAppLoad', function ($router, $renderer): void {
            $router->add('/example', function (): void {
                exit('das');
            });
        });

        $eventHandler->on('backup.onBackupTake', function ($path): void {

        });
    }
}
