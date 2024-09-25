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
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Plugins\Interfaces\Stability;

class Database extends MySQL implements Stability
{
    /**
     * Get the database.
     * 
     * @return \MythicalSystemsFramework\Database\MySQL
     */
    public static function getDatabase(): MySQL
    {
        return new MySQL();
    }

    /**
     * Register a new plugin in the database.
     *
     * @param string $name The name of the plugin
     * @param string $description The description of the plugin
     * @param string|null $homepage The homepage of the plugin
     * @param string|null $require The require of the plugin
     * @param string|null $license The license of the plugin
     * @param Stability|string $stability The stability of the plugin
     * @param array|string $authors The authors of the plugin
     * @param string|null $support The support link of the plugin
     * @param string|null $funding The funding of the plugin
     * @param string $version The version of the plugin
     * @param bool $isCheck Is this is a check?
     * 
     * @return int The plugin id
     */
    public static function registerNewPlugin(string $name, string $description, ?string $homepage, string|array $require, ?string $license, Stability|string $stability, array|string $authors, ?string $support, ?string $funding, string $version, bool $isCheck): int
    {
        try {   
            if ($isCheck == true) {
                return 1;
            }
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            if (self::doesInfoExist('name', $name)) {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, "A plugin with the name $name already exists.");
                return 0;
            }
            $stmt = $conn->prepare('INSERT INTO framework_plugins (name,description,homepage,`require`,license,stability,authors,support,funding,version) VALUES (?,?,?,?,?,?,?,?,?,?)');
            $authorsString = is_array($authors) ? implode(', ', $authors) : $authors;
            $requireString = is_array($require) ? implode(', ', $require) : $require;

            $stmt->bind_param('ssssssssss', $name, $description, $homepage, $requireString, $license, $stability, $authorsString, $support, $funding, $version);
            $stmt->execute();

            return $stmt->insert_id;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while registering a new plugin: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Does info exist?
     * 
     * @param string $info The info to check
     * @param string $value The value to check
     * 
     * @return bool Does the info exist?
     */
    public static function doesInfoExist(string $info, string $value): bool
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_plugins WHERE `$info` = ?");
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->num_rows > 0;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while checking if a plugin info exists: ' . $e->getMessage());

            return true;
        }
    }

    /**
     * Get the plugin info from db.
     *
     * @param string $name The name of the plugin
     * 
     * @return array The plugin info
     */
    public static function getPlugin(string $name): array
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_plugins WHERE `name` = ?');
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_assoc();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while getting a plugin info: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * UnRegister a plugin.
     *
     * @param string $name The name of the plugin
     * 
     * @return void 
     */
    public static function unRegisterPlugin(string $name): void
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_plugins WHERE `name` = ?');
            $stmt->bind_param('s', $name);
            $stmt->execute();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while unregistering a plugin: ' . $e->getMessage());
        }
    }

    /**
     * Update a plugin info.
     *
     * @param string $plugin The plugin name
     * @param string $column The column from the database
     * @param string $value The value to update
     * 
     * @return void
     */
    public static function updatePlugin(string $plugin, string $column, string $value): void
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare("UPDATE framework_plugins SET `$column` = ? WHERE `name` = ?");
            $stmt->bind_param('ss', $value, $plugin);
            $stmt->execute();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while updating a plugin: ' . $e->getMessage());
        }
    }

    /**
     * Get a specific plugin info.
     *
     * @param string $plugin The plugin name!
     * @param string $column The info you are looking for!
     * 
     * @return string The info
     */
    public static function getPluginInfo(string $plugin, string $column): string
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare("SELECT $column FROM framework_plugins WHERE `name` = ?");
            $stmt->bind_param('s', $plugin);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return $row[$column];
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while getting a plugin info: ' . $e->getMessage());

            return '';
        }
    }

    /**
     * Get all the plugins from the database.
     * 
     * @return array The plugins
     */
    public static function getAllPlugins(): array
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_plugins ORDER BY `framework_plugins`.`id` DESC');
            $stmt->execute();
            $result = $stmt->get_result();
            $plugins = [];
            while ($row = $result->fetch_assoc()) {
                $plugins[] = $row;
            }

            return $plugins;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while getting all plugins: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * 
     * Get all the plugins from the database.
     * 
     * @param string $plugin_name The name of the plugin
     * 
     * @return array The permissions
     */
    public static function getAllRegisteredPermissionsByPlugin(string $plugin_name): array
    {
        try {
            if (PluginCompilerHelper::doesPluginExist($plugin_name) == false) {
                return [];
            } else {
                $plugin_id = self::getPluginInfo($plugin_name, 'id');
            }

            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_roles_permissions_list WHERE `owned_by` = "plugin" AND `owned_by_id` = ?');
            $stmt->bind_param('s', $plugin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $permissions = [];
            while ($row = $result->fetch_assoc()) {
                $permissions[] = $row;
            }

            return $permissions;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while getting all permissions: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * Get all the registered permissions.
     * 
     * @return array The permissions
     */
    public static function getAllRegisteredPermissions(): array
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_roles_permissions_list');
            $stmt->execute();
            $result = $stmt->get_result();
            $permissions = [];
            while ($row = $result->fetch_assoc()) {
                $permissions[] = $row;
            }

            return $permissions;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while getting all permissions: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * 
     * Get all the plugins from the database.
     * 
     * @param array $permissions The permissions
     * @param string $plugin_name The name of the plugin
     * 
     * @return bool True if the permission already exists
     */
    public static function doRegisteredPermissionAlreadyExists(array $permissions, string $plugin_name): bool
    {
        $db_permissions = self::getAllRegisteredPermissionsByPlugin($plugin_name);
        foreach ($permissions as $permission) {
            foreach ($db_permissions as $db_permission) {
                if ($permission == $db_permission['permission']) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Register a permission.
     * 
     * @param string $permission The permission
     * 
     * @return bool True if the permission was registered
     */
    public static function doesPermissionAlreadyExist(string $permission): bool
    {
        $db_permissions = self::getAllRegisteredPermissions();
        foreach ($db_permissions as $db_permission) {
            if ($permission == $db_permission['permission']) {
                return true;
            }
        }
        return false;
    }
    /**
     * Register a permission.
     * 
     * @param array $permissions The permissions
     * @param string $plugin_name The name of the plugin
     * 
     * @return void
     */
    public static function registerPermission(array $permissions, string $plugin_name): void
    {

        try {
            foreach ($permissions as $permission) {
                if (self::doesPermissionAlreadyExist($permission) == false) {
                    if (PluginCompilerHelper::doesPluginExist($plugin_name) == false) {
                        return;
                    }
                    $plugin_id = self::getPluginInfo($plugin_name, 'id');
                    if ($plugin_id == '') {
                        return;
                    }

                    $db = self::getDatabase();
                    $conn = $db->connectMYSQLI();
                    $stmt = $conn->prepare('INSERT INTO framework_roles_permissions_list (permission, owned_by_id) VALUES (?, ?)');
                    $stmt->bind_param('si', $permission, $plugin_id);
                    $stmt->execute();
                }
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while registering a permission: ' . $e->getMessage());
        }
    }
    /**
     * Get the plugin name by id.
     * 
     * @param int $id The id of the plugin
     * 
     * @return string The plugin name
     */
    public static function getPluginNameById(int $id): string
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT name FROM framework_plugins WHERE `id` = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return $row['name'];
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while getting a plugin name by id: ' . $e->getMessage());

            return '';
        }
    }

    /**
     * Remove a permission.
     * 
     * @param string $permission The permission
     * 
     * @return void
     */
    public static function removePermission(string $permission): void
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_roles_permissions_list WHERE `permission` = ?');
            $stmt->bind_param('s', $permission);
            $stmt->execute();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while removing a permission: ' . $e->getMessage());
        }
    }
    /**
     * 
     * Check if a plugin exists!
     * 
     * @param int $id The id of the plugin
     * 
     * @return bool
     */
    public static function doesPluginExistID(int $id) : bool {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_plugins WHERE `id` = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->num_rows > 0;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while checking if a plugin exists: ' . $e->getMessage());

            return true;
        }
    }
    /**
     * 
     * Check if a plugin exists!
     * 
     * @param int $plugin_id The id of the plugin
     * 
     * @return void
     */
    public static function purgePermissions(int $plugin_id) : void {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_roles_permissions_list WHERE `owned_by_id` = ?');
            $stmt->bind_param('i', $plugin_id);
            $stmt->execute();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::PLUGIN, 'An error occurred while purging permissions: ' . $e->getMessage());
        }
    }
}
