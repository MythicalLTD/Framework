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
     * @param string $plugin The plugin name
     * @param string $column The info you are looking for
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
     */
    public static function getAllPlugins(): array
    {
        try {
            $db = self::getDatabase();
            $conn = $db->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_plugins');
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
}
