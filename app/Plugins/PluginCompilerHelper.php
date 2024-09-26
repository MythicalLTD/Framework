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
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class PluginCompilerHelper
{
    public static string $plugins_path = __DIR__ . '/../../storage/addons';

    /**
     * Ensure the plugin path exists.
     */
    public static function ensurePluginPathExists(): void
    {
        if (!file_exists(self::$plugins_path)) {
            mkdir(self::$plugins_path, 0777, true);
        }
    }

    /**
     * Check the requirements of a plugin.
     */
    public static function checkPluginRequirements(string $plugin, array $plugin_info): void
    {
        if (isset($plugin_info['require'])) {
            $requirements = $plugin_info['require'];
            foreach ($requirements as $requirement) {
                // Skip default plugin
                if ($requirement == 'MythicalSystemsFramework') {
                    continue;
                }

                // Check if the requirement is a composer package
                if (strpos($requirement, 'composer=') === 0) {
                    $composerVersion = substr($requirement, strlen('composer='));
                    if (\Composer\InstalledVersions::isInstalled($composerVersion, true)) {
                        continue;
                    }
                    Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Plugin $plugin requires composer package $composerVersion to be installed.");
                }

                // Check if the requirement is a php version
                if (strpos($requirement, 'php=') === 0) {
                    $phpVersion = substr($requirement, strlen('php='));
                    if (version_compare(PHP_VERSION, $phpVersion, '<')) {
                        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Plugin $plugin requires PHP version $phpVersion or higher.");
                    } else {
                        continue;
                    }
                }

                // Check if the requirement is a php extension
                if (strpos($requirement, 'php-ext=') === 0) {
                    $ext = substr($requirement, strlen('php-ext='));
                    if (!extension_loaded($ext)) {
                        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Plugin $plugin requires PHP extension $ext to be installed.");
                    } else {
                        continue;
                    }
                }

                // Check if the requirement is a plugin
                $isInstalled = self::readPluginFile($requirement);
                if ($isInstalled) {
                    continue;
                }
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Plugin $plugin requires $requirement to be installed.");
            }
        }
    }

    /**
     * Check if a plugin is installed.
     *
     * @param array $plugins The plugins to check
     */
    public static function installCheck(array $plugins): void
    {
        $plugins = array_reverse($plugins);
        foreach ($plugins as $plugin) {
            $plugin_info = self::readPluginFile($plugin);
            if (empty($plugin_info)) {
                Logger::log(LoggerLevels::ERROR, LoggerTypes::PLUGIN, "Failed to read plugin info for $plugin.");
                continue;
            }
            if (Database::doesInfoExist('name', $plugin_info['name']) == false) {
                self::registerPluginIfNotRegistered($plugin_info);
            } else {
                self::updatePluginIfOutdated($plugin_info);
            }
        }
    }

    /**
     * Register a plugin if it is not already registered.
     */
    public static function registerPluginIfNotRegistered(array $plugin_info): void
    {
        $p = $plugin_info;
        $p_homepage = $p['homepage'] ?? null;
        $p_license = $p['license'] ?? null;
        $p_support = $p['support'] ?? null;
        $p_funding = $p['funding'] ?? null;
        $p_require = $p['require'] ?? 'MythicalSystemsFramework';
        Database::registerNewPlugin($p['name'], $p['description'], $p_homepage, $p_require, $p_license, $p['stability'], $p['authors'], $p_support, $p_funding, $p['version'], false);
    }

    /**
     * Update a plugin if it is outdated.
     */
    public static function updatePluginIfOutdated(array $plugin_info): void
    {
        if (Database::doesInfoExist('name', $plugin_info['name']) == true) {
            $plugin_info_db = Database::getPlugin($plugin_info['name']);
            if ($plugin_info_db['enabled'] == 'true') {
                $version_db = $plugin_info_db['version'];
                $version_filesystem = $plugin_info['version'];

                if ($version_db != $version_filesystem) {
                    $plugin_db_info_enabled = $plugin_info_db['enabled'];
                    Database::unRegisterPlugin($plugin_info['name']);
                    $p = $plugin_info;

                    $p_homepage = $p['homepage'] ?? null;
                    $p_license = $p['license'] ?? null;
                    $p_support = $p['support'] ?? null;
                    $p_funding = $p['funding'] ?? null;
                    $p_require = $p['require'] ?? 'MythicalSystemsFramework';
                    Database::registerNewPlugin($p['name'], $p['description'], $p_homepage, $p_require, $p_license, $p['stability'], $p['authors'], $p_support, $p_funding, $p['version'], false);
                    Database::updatePlugin($plugin_info['name'], 'enabled', $plugin_db_info_enabled);
                    Logger::log(LoggerLevels::INFO, LoggerTypes::PLUGIN, 'Plugin ' . $plugin_info['name'] . ' has been updated to version ' . $plugin_info['version']);
                }
            }
        }
    }

    /**
     * Is a plugin enabled?
     */
    public static function isPluginEnabled(string $plugin_name): bool
    {
        return Database::getPluginInfo($plugin_name, 'enabled') == 'true';
    }

    /**
     * Enable a plugin.
     */
    public static function enablePlugin(string $plugin, array $plugin_info, PluginEvent $eventHandler, bool $skipEventEnable = false): void
    {
        $plugin_info_db = Database::getPlugin($plugin_info['name']);
        $plugin_home_dir = self::$plugins_path . '/' . $plugin_info['name'];
        $main_class = $plugin_home_dir . '/' . $plugin_info_db['name'] . '.php';
        if (self::isPluginEnabled($plugin_info['name']) == false) {
            return;
        }
        if (file_exists($main_class)) {
            try {
                require_once $main_class;
                $plugin_class = new $plugin_info_db['name']();
                if (self::isPluginInstalled($plugin_info['name']) == false) {
                    $plugin_class->onInstall();
                    Database::updatePlugin($plugin_info['name'], 'isInstalled', 'true');
                }
                // Register lang
                if (self::doesPluginExtendLang($plugin_info['name'])) {
                    $server_lang = Settings::getSetting('app', 'lang');
                    if (!self::doesPluginHaveLanguage($plugin_info['name'], $server_lang)) {
                        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "The plugin '" . $plugin_info['name'] . "' does not have a language file for the server language. Using default language.");
                    }
                }
                $plugin_class->Main();
                try {
                    if (!$skipEventEnable) {
                        $plugin_class->Event($eventHandler);
                    }
                } catch (\Exception $e) {
                    Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'Failed to add events for plugin' . $plugin_info_db['name'] . '' . $e->getMessage());
                }
            } catch (\Exception $e) {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "Something failed while we tried to enable the plugin '" . $plugin_info_db['name'] . "'. " . $e->getMessage());
            }
        } else {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "The main class for plugin '$plugin' does not exist.");
        }
    }

    /**
     * Get all plugins.
     */
    public static function getAllPlugins(): array
    {
        self::ensurePluginPathExists();
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
     * Check if a plugin is installed.
     *
     * @param string $plugin_name The name of the plugin
     *
     * @return bool True if yes, false if no!
     */
    public static function isPluginInstalled(string $plugin_name): bool
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        if (!self::isPluginConfigValid($plugin_name)) {
            return false;
        }

        if (Database::getPluginInfo($plugin_name, 'isInstalled') == 'true') {
            return true;
        }

        return false;
    }

    /**
     * Get plugin info.
     */
    public static function readPluginFile(string $plugin_name): array
    {
        self::ensurePluginPathExists();
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
     */
    public static function doesPluginExist(string $plugin_name): bool
    {
        self::ensurePluginPathExists();
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/MythicalFramework.json';

        return file_exists($plugin_folder);
    }

    /**
     * Is the plugin config valid?
     *
     * @param string $plugin_name The name of the plugin
     */
    public static function isPluginConfigValid(string $plugin_name): bool
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        $json_file = self::$plugins_path . '/' . $plugin_name . '/MythicalFramework.json';
        if (file_exists($json_file)) {
            $json = json_decode(file_get_contents($json_file), true);
            if (isset($json['name']) && isset($json['description']) && isset($json['stability']) && isset($json['authors']) && isset($json['version']) && isset($json['require'])) {
                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * Does a plugin have a cron job folder?
     *
     * @return bool
     */
    public static function doesPluginHaveCron(string $plugin_name)
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/crons';

        return file_exists($plugin_folder);
    }

    /**
     * Get all cron files for a plugin.
     *
     * @return array
     */
    public static function getPluginCronFiles(string $plugin_name)
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return [];
        }
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/crons';
        $files = [];
        foreach (scandir($plugin_folder) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $files[] = $file;
        }

        return $files;
    }

    /**
     * Does a plugin have a lang folder?
     *
     * @param string $plugin_name The name of the plugin
     *
     * @return bool True if yes, false if no!
     */
    public static function doesPluginExtendLang(string $plugin_name): bool
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/lang';
        if (!file_exists($plugin_folder) || !is_dir($plugin_folder)) {
            return false;
        }
        foreach (scandir($plugin_folder) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'yml') {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all lang files for a plugin.
     *
     * @param string $lang The language you want to get the files for
     */
    public static function doesPluginHaveLanguage(string $plugin_name, string $lang): bool
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/lang';
        if (!file_exists($plugin_folder) || !is_dir($plugin_folder)) {
            return false;
        }

        return file_exists($plugin_folder . '/' . $lang . '.yml');
    }

    /**
     * Register plugin permissions.
     */
    public static function registerPluginPermissions(): void
    {
        self::ensurePluginPathExists();
        $plugins = self::getAllPlugins();
        foreach ($plugins as $plugin) {

            if (self::isPluginEnabled($plugin) == true) {
                if (self::doesPluginHavePermissions($plugin)) {
                    $permissions = self::getPluginPermissions($plugin);
                    Database::registerPermission($permissions, $plugin);
                    continue;
                }
            }
        }
    }

    /**
     * Does a plugin have a permissions folder?
     *
     * @param string $plugin_name The name of the plugin
     *
     * @return bool True if yes, false if no!
     */
    public static function doesPluginHavePermissions(string $plugin_name): bool
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return false;
        }
        $plugin_folder = self::$plugins_path . '/' . $plugin_name . '/permissions.json';
        if (file_exists($plugin_folder)) {
            return true;
        }

        return false;
    }

    /**
     * Get the permissions file for a plugin.
     *
     * @param string $plugin_name The name of the plugin
     */
    public static function getPluginPermissions(string $plugin_name): array
    {
        self::ensurePluginPathExists();
        if (!self::doesPluginExist($plugin_name)) {
            return [];
        }
        $permission_file = self::$plugins_path . '/' . $plugin_name . '/permissions.json';
        if (!file_exists($permission_file)) {
            return [];
        }

        return json_decode(file_get_contents($permission_file), true);
    }

    /**
     * Check if a plugin is missing.
     *
     * @param string $plugin_name The name of the plugin
     */
    public static function checkIfPluginIsMissing(string $plugin_name): void
    {
        $plugin_info_db = Database::getPlugin($plugin_name);

        if ($plugin_info_db) {
            if (!self::doesPluginExist($plugin_name)) {
                Database::unRegisterPlugin($plugin_name);
                Logger::log(LoggerLevels::INFO, LoggerTypes::PLUGIN, "Plugin $plugin_name was missing from the plugins folder and has been removed from the database.");
            }
        }
    }

    /**
     * Run the plugins install check.
     */
    public static function runPluginsInstallCheck(): void
    {
        $plugins = Database::getAllPlugins();
        foreach ($plugins as $plugin) {
            self::checkIfPluginIsMissing($plugin['name']);
        }
    }

    /**
     * Get the path to the language file.
     *
     * @return string
     */
    public static function getLanguagePaths(): array
    {
        $plugins = self::getAllPlugins();
        $languagePaths = [];

        foreach ($plugins as $plugin) {
            if (self::isPluginEnabled($plugin) == true) {
                if (self::doesPluginExtendLang($plugin)) {
                    $languagePaths[] = self::$plugins_path . '/' . $plugin . '/lang/' . Settings::getSetting('app', 'lang') . '.yml';
                }
            }
        }

        return $languagePaths;
    }

    /**
     * Remove ghost permissions.
     */
    public static function removeGhostPermissions(): void
    {
        $permissions = Database::getAllRegisteredPermissions();
        $plugins = self::getAllPlugins();
        foreach ($permissions as $permission) {
            $plugin_owner = $permission['owned_by_id'];
            if (Database::doesPluginExistID($plugin_owner) == false) {
                Database::purgePermissions($plugin_owner);
            }
        }
    }
}
