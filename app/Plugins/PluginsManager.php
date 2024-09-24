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

namespace MythicalSystemsFramework\Plugins;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class PluginsManager extends PluginCompilerHelper
{
    /**
     * Init the plugins.
     */
    public static function init(PluginEvent $eventHandler): void
    {
        self::ensurePluginPathExists();
        $plugins = self::getAllPlugins();
        foreach ($plugins as $plugin) {
            $plugin_info = self::readPluginFile($plugin);
            self::checkPluginRequirements($plugin, $plugin_info);
            self::registerPluginIfNotRegistered($plugin_info);
            self::updatePluginIfOutdated($plugin_info);
            self::enablePlugin($plugin, $plugin_info, $eventHandler);
            self::runPluginsInstallCheck();
            self::registerPluginPermissions();
        }
    }

    /**
     * Init the cron jobs.
     */
    public static function initCron(PluginEvent $eventHandler): void
    {
        self::ensurePluginPathExists();
        $plugins = self::getAllPlugins();
        foreach ($plugins as $plugin) {
            self::runPluginsInstallCheck();
            $plugin_info = self::readPluginFile($plugin);
            self::checkPluginRequirements($plugin, $plugin_info);
            self::registerPluginIfNotRegistered($plugin_info);
            self::updatePluginIfOutdated($plugin_info);
            self::registerPluginPermissions();
            self::enablePlugin($plugin, $plugin_info, $eventHandler, true);
            if (self::doesPluginHaveCron($plugin)) {
                $crons = self::getPluginCronFiles($plugin);
                foreach ($crons as $cron) {
                    $cron = __DIR__ . '/../../storage/addons/' . $plugin . '/crons/' . $cron;
                    if (file_exists($cron)) {
                        require $cron;
                    } else {
                        Logger::log('Cron file not found: ' . $cron, LoggerTypes::PLUGIN, LoggerLevels::ERROR);
                    }
                }
            }
        }
    }
}
