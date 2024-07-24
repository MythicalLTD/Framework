<?php

namespace MythicalSystemsFramework\Plugins;

class PluginsManager {
    /**
     * Get all plugins
     * 
     * @return array
     */
    public static function getAllPlugins() : array {
        $plugins = [];
        $addonsDir = __DIR__ . '/../../addons';
        $addonDirs = scandir($addonsDir);
        
        foreach ($addonDirs as $addonDir) {
            if ($addonDir === '.' || $addonDir === '..') {
            continue;
            }
            
            $pluginDir = $addonsDir . '/' . $addonDir;
            $pluginConfigFile = $pluginDir . '/MythicalFramework.json';
            
            $plugins[] = $addonDir;

            
        }
        
        return $plugins;
    }
}