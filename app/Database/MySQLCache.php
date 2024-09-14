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

namespace MythicalSystemsFramework\Database;

use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\Managers\Settings;

class MySQLCache extends MySQL
{
    /**
     * Summary of saveCache.
     *
     * @param string $table_name The name of the table
     */
    public static function saveCache(string $table_name): string
    {
        try {
            Debugger::HideAllErrors();
            if (self::doesTableExist($table_name) == false) {
                return 'ERROR_TABLE_DOES_NOT_EXIST';
            }

            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $query = 'SELECT * FROM ' . mysqli_real_escape_string($conn, $table_name);
            $result = $conn->query($query);

            if ($result->num_rows == 0) {
                return 'ERROR_NO_DATA_FOUND_IN_TABLE';
            }

            /*
             * Specific table dump settings!
             *
             * @requires framework_settings
             */
            if ($table_name == 'framework_settings') {
                /**
                 * Code to export the settings file in a format that MythicalSystemsFramework\Managers\Settings can read!
                 */
                $query = 'SELECT scategory FROM ' . mysqli_real_escape_string($conn, $table_name);
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    $categories = [];
                    while ($row = $result->fetch_assoc()) {
                        $category = $row['scategory'];
                        if (!in_array($category, $categories)) {
                            $categories[] = $category;
                        }
                    }
                }
                $data = [];
                foreach ($categories as $category) {
                    if ($category !== 0) {
                        $data[$category] = [];

                        $query = 'SELECT skey, svalue FROM ' . mysqli_real_escape_string($conn, $table_name) . " WHERE scategory = '" . mysqli_real_escape_string($conn, $category) . "'";
                        $result = $conn->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $name = $row['skey'];
                                $value = $row['svalue'];
                                $data[$category][$name] = $value;
                            }
                        }
                    }
                }
            } elseif ($table_name == 'framework_users') {
                // TODO: ADD SUPPORT FOR DUMP USERS IN CACHE!
            } else {
                return 'ERROR_TABLE_NOT_SUPPORTED';
            }

            $cache_info['cache_info'] = [
                'table' => $table_name,
                'date_created' => date('Y-m-d H:i:s'),
                'date_expire' => date('Y-m-d H:i:s', strtotime('+' . $data['caches']['settings_cache_life'] . ' seconds')),
            ];
            $data = array_merge($cache_info, $data);
            $json = json_encode($data, JSON_PRETTY_PRINT);
            Settings::up();
            file_put_contents(Settings::$cache_path . '/' . $table_name . '.json', $json);

            return 'OK';
        } catch (\Exception $e) {
            return 'ERROR_MYSQL_ERROR';
        }
    }

    public static function deleteCaches(): void
    {
        $files = glob(Settings::$cache_path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
