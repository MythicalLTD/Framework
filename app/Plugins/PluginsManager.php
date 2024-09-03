<?php

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
        }
    }

    /** 
     * Init the cron jobs.
     * @return void
     */
    public static function initCron(PluginEvent $eventHandler): void
    {
        self::ensurePluginPathExists();
        $plugins = self::getAllPlugins();
        foreach ($plugins as $plugin) {
            $plugin_info = self::readPluginFile($plugin);
            self::checkPluginRequirements($plugin, $plugin_info);
            self::registerPluginIfNotRegistered($plugin_info);
            self::updatePluginIfOutdated($plugin_info);
            self::enablePlugin($plugin, $plugin_info, $eventHandler, true);
            if (self::doesPluginHaveCron($plugin)) {
                $crons = self::getPluginCronFiles($plugin);
                foreach ($crons as $cron) {
                    $cron = __DIR__.'/../../storage/addons/'.$plugin.'/crons/'.$cron;
                    if (file_exists($cron)) {
                        require $cron;
                    } else {
                        Logger::log('Cron file not found: '.$cron, LoggerTypes::PLUGIN, LoggerLevels::ERROR);
                    }
                }
            }
        }
    }
}
