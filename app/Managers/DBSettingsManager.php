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

namespace MythicalSystemsFramework\Managers;

use MythicalSystemsFramework\Database\MySQL;

/**
 * DEPRECATED: Use Settings class instead!!
 */
class DBSettingsManager
{
    /**
     * DEPRECATED: Use Settings class instead!!
     * Set a setting in the database!
     *
     * @param string $category The category of the setting you want to set!
     * @param string $key The key of the setting you want to set!
     * @param string $value The value of the setting you want to set!
     *
     * @return bool True if successfully set, false otherwise!
     */
    public static function set(string $category, string $key, string $value): bool
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $stmt = $conn->prepare('INSERT INTO framework_settings (`scategory`, `skey`, `svalue`) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $category, $key, $value);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * DEPRECATED: Use Settings class instead!!
     * Delete a setting from the database!
     *
     * @param string $category The category of the setting you want to delete!
     * @param string $key The key of the setting you want to delete!
     *
     * @return bool True if successfully deleted, false otherwise!
     */
    public static function delete(string $category, string $key): bool
    {
        if (self::exists($category, $key)) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_settings WHERE `skey` = ? AND `scategory` = ?');
            $stmt->bind_param('s', $key);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        }

        return false;

    }

    /**
     * DEPRECATED: Use Settings class instead!!
     *
     * Delete all settings from the database!
     *
     * @return bool True if successfully deleted, false otherwise!
     */
    public static function deleteAll(): bool
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $success = $conn->query('TRUNCATE TABLE framework_settings');

        return $success;
    }

    /**
     * DEPRECATED: Use Settings class instead!!
     *
     * Update a setting in the database!
     *
     * @param string $category The category of the setting you want to update!
     * @param string $key The key of the setting you want to update!
     * @param string $value The value of the setting you want to update!
     *
     * @return bool True if successfully updated, false otherwise!
     */
    public static function update(string $category, string $key, string $value): bool
    {
        if (self::exists($category, $key)) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('UPDATE framework_settings SET `svalue` = ? WHERE `skey` = ? AND `scategory` = ?');
            $stmt->bind_param('sss', $value, $key, $category);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        }

        return false;

    }

    /**
     * DEPRECATED: Use Settings class instead!!
     *
     * Check if a setting exists in the database!
     *
     * @param string $category The category of the setting you want to check!
     * @param string $key The key of the setting you want to check!
     *
     * @return bool True if the setting exists, false otherwise!
     */
    public static function exists(string $category, string $key): bool
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $stmt = $conn->prepare('SELECT COUNT(*) FROM framework_settings WHERE `skey` = ? AND `scategory` = ?');
        $stmt->bind_param('ss', $key, $category);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0;
    }
}
