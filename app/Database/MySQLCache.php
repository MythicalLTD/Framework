<?php

namespace MythicalSystemsFramework\Database;

use Exception;
use MythicalSystems\Api\Api;
use MythicalSystemsFramework\Managers\Settings;

class MySQLCache extends MySQL
{
    public static function saveCache(string $table_name): void
    {
        try {
            if (self::doesTableExist($table_name) == false) {
                throw new Exception("Table does not exist.");
            }

            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $query = "SELECT * FROM " . mysqli_real_escape_string($conn, $table_name);
            $result = $conn->query($query);

            if ($result->num_rows == 0) {
                throw new Exception("No data found.");
            }

            /**
             * Specific table dump settings!
             * 
             * @requires framework_settings
             */
            if ($table_name == "framework_settings") {
                /**
                 * Code to export the settings file in a format that MythicalSystemsFramework\Managers\Settings can read!
                 */
                $query = "SELECT scategory FROM " . mysqli_real_escape_string($conn, $table_name);
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

                        $query = "SELECT skey, svalue FROM " . mysqli_real_escape_string($conn, $table_name) . " WHERE scategory = '" . mysqli_real_escape_string($conn, $category) . "'";
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

            } elseif ($table_name == "framework_users") {

            } else {
                throw new Exception("Table not supported.");
            }

            $cache_info["cache_info"] = [
                "table" => $table_name,
                "date_created" => date("Y-m-d H:i:s"),
                "date_expire" => date("Y-m-d H:i:s", strtotime("+".$data["caches"]["settings_cache_life"]." seconds")),
            ];
            $data = array_merge($cache_info,$data);
            $json = json_encode($data, JSON_PRETTY_PRINT);
            Settings::up();
            file_put_contents(Settings::$cache_path . '/' . $table_name . '.json', $json);
        } catch (Exception $e) {
            throw new Exception("Failed to save cache: " . $e);
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