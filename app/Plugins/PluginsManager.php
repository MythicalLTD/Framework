<?php

namespace MythicalSystemsFramework\Plugins;

use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\Config;
use MythicalSystemsFramework\Encryption\XChaCha20;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Handlers\ActivityHandler;
use MythicalSystemsFramework\Managers\Settings as settings;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Database\MySQL;

class PluginsManager
{
    public static string $plugins_path = __DIR__ . "/../../storage/addons";
    public static function init(\Router\Router $router, \Twig\Environment $renderer): void
    {
        if (!file_exists(self::$plugins_path)) {
            mkdir(self::$plugins_path, 0777, true);
        }

        $plugins = self::getAllPlugins();
        foreach ($plugins as $plugin) {
            $plugin_info = self::readPluginFile($plugin);
            /**
             * Are all the requirements installed?
             */
            if (isset($plugin_info['require'])) {
                $requirements = $plugin_info['require'];
                foreach ($requirements as $requirement) {
                    if ($requirement == "MythicalSystemsFramework") {
                        continue;
                    }
                    $isInstalled = self::readPluginFile($requirement);
                    if ($isInstalled) {
                        continue;
                    } else {
                        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Plugin $plugin requires $requirement to be installed.");
                    }
                }
            }

            /**
             * Register the plugin in the database if it is not already registered.
             */
            if (!Database::doesInfoExist("name", $plugin_info['name']) == true) {
                $p = $plugin_info;

                $p_homepage = $p['homepage'] ?? null;
                $p_license = $p['license'] ?? null;
                $p_support = $p['support'] ?? null;
                $p_funding = $p['funding'] ?? null;
                $p_require = $p['require'] ?? "MythicalSystemsFramework";
                Database::registerNewPlugin($p['name'], $p['description'], $p_homepage, $p_require, $p_license, $p['stability'], $p['authors'], $p_support, $p_funding, $p['version'], false);
                continue;
            }

            /**
             * Is plugin enabled?
             */
            $plugin_info_db = Database::getPlugin($plugin_info['name']);
            if ($plugin_info_db['enabled'] == "true") {
                $plugin_home_dir = self::$plugins_path . '/' . $plugin_info['name']; 
                $main_class = $plugin_home_dir. '/' . $plugin_info_db["name"] . ".php";
                if (file_exists($main_class)) {
                    /**
                     * Start the plugin main class.
                     */
                    try {
                        require_once($main_class);
                        $plugin_class = new $plugin_info_db["name"];
                        $plugin_class->Main();
                    } catch (\Exception $e) {
                        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Something failed while we tried to enable the plugin '" . $plugin_info_db["name"] . "'. " . $e->getMessage());
                    }
                    /**
                     * Do we have extends?
                     */
                    $boolDoWeHaveExtends = false;
                    if (is_dir($plugin_home_dir. '/extends')) {
                        $boolDoWeHaveExtends = true; 
                    }
                    // Let's check if we have events, routes, commands, and frontend.
                    $boolDoWeHaveEvents = false;
                    $boolDoWeHaveRoutes = false;
                    $boolDoWeHaveCommands = false; 
                    $boolDoWeHaveFrontend = false; 

                    if ($boolDoWeHaveExtends) {
                        if (is_dir($plugin_home_dir.'/extends/events')) {
                            $boolDoWeHaveEvents = true;
                        }
                        if (is_dir($plugin_home_dir.'/extends/routes')) {
                            $boolDoWeHaveRoutes = true;
                        }
                        if (is_dir($plugin_home_dir.'/extends/commands')) {
                            $boolDoWeHaveCommands = true;
                        }
                        if (is_dir($plugin_home_dir.'/extends/frontend')) {
                            $boolDoWeHaveFrontend = true;
                        }
                    }


                } else {
                    Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "The main class for plugin '$plugin' does not exist.");
                }
            }
        }
    }

    /**
     * Get all plugins.
     */
    public static function getAllPlugins(): array
    {
        $plugins = [];
        foreach (scandir(self::$plugins_path) as $plugin) {
            if ($plugin == '.' || $plugin == '..') {
                continue;
            }
            $pluginPath = self::$plugins_path . '/' . $plugin;
            if (is_dir($pluginPath)) {
                $json_file = $pluginPath . '/MythicalFramework.json';
                if (file_exists($json_file)) {
                    $json = json_decode(file_get_contents($json_file), true);
                    if (isset($json['name']) && $json['name'] === $plugin) {
                        $plugins[] = $plugin;
                    }
                }
            }
        }
        return $plugins;
    }

    /**
     * Get plugin info.
     */
    public static function readPluginFile(string $plugin_name): array
    {
        if (!self::doesPluginExist($plugin_name)) {
            return [];
        }
        if (!self::isPluginConfigValid($plugin_name)) {
            return [];
        }
        $json_file = self::$plugins_path . '/' . $plugin_name . '/MythicalFramework.json';
        return json_decode(file_get_contents($json_file), true);
    }

    /**
     * Does a plugin exist?
     * 
     * @param string $plugin_name The name of the plugin
     * 
     * @return bool
     */
    public static function doesPluginExist(string $plugin_name): bool
    {
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/MythicalFramework.json';
        return file_exists($plugin_folder);
    }

    /**
     * Is the plugin config valid?
     * 
     * @param string $plugin_name The name of the plugin
     * 
     * @return bool 
     */
    public static function isPluginConfigValid(string $plugin_name): bool
    {
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        $json_file = self::$plugins_path . '/' . $plugin_name . '/MythicalFramework.json';
        if (file_exists($json_file)) {
            $json = json_decode(file_get_contents($json_file), true);
            if (isset($json['name']) && isset($json['description']) && isset($json['stability']) && isset($json['authors']) && isset($json['version']) && isset($json['require'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
