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

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Plugins\PluginBuilder;

class Core implements PluginBuilder
{
    public function Main(): void
    {
        // TODO: Implement the main function
    }

    public function Event(MythicalSystemsFramework\Plugins\PluginEvent $eventHandler): void
    {
    }

    public function onInstall(): void
    {
        Logger::log(LoggerLevels::INFO, LoggerTypes::PLUGIN, 'Core plugin installed');
    }

    public function onUninstall(): void
    {
        Logger::log(LoggerLevels::INFO, LoggerTypes::PLUGIN, 'Core plugin uninstalled');
    }

    public function onUpdate(): void
    {
        Logger::log(LoggerLevels::INFO, LoggerTypes::PLUGIN, 'Core plugin updated');
    }
}
