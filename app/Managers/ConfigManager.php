<?php

namespace MythicalSystemsFramework\Managers;

use Exception;
use MythicalSystems\Helpers\ConfigHelper;

class ConfigManager
{
    private static string $configpath = __DIR__ . "/../../settings.json";
    /**
     * DEPRECATED: Use Settings class instead!!
     * DEPRECATED: This class is used for the settings.json file!
     *
     * Get value form the config!
     *
     * @param string $category The category of the value you want to take from the config
     * @param string $value The value you want to take from the config file!
     *
     * @return string|null
     */
    public static function get(string $category, string $key): string|null
    {
        try {
            if (!file_exists(self::$configpath)) {
                die("Config file not found!");
            }
            if (!is_writable(self::$configpath)) {
                die("We have no access to the config file!");
            } else {
                $config = new ConfigHelper(self::$configpath);
            }
        } catch (Exception $e) {
            die("Failed to init the config class! \n" . $e->__toString());
        }
        return $config->get($category, $key);
    }

    /**
     * DEPRECATED: Use Settings class instead!!
     * DEPRECATED: This class is used for the settings.json file!
     *
     * Set a value in the config file!
     *
     * @param string $category The category of the value you want to set in the config
     * @param string $value The value you want to set in the config file!
     *
     * @return bool If true then success if false then false!
     */
    public static function set(string $category, string $key, string $value): bool
    {
        try {
            if (!file_exists(self::$configpath)) {
                die("Config file not found!");
            }
            if (!is_writable(self::$configpath)) {
                die("We have no access to the config file!");
            } else {
                $config = new ConfigHelper(self::$configpath);
            }
        } catch (Exception $e) {
            die("Failed to init the config class! \n" . $e->__toString());
        }
        return $config->set($category, $key, $value);
    }

    /**
     * DEPRECATED: Use Settings class instead!!
     * DEPRECATED: This class is used for the settings.json file!
     *
     * Add a value to the config file!
     *
     * @param string $category The category of the value you want to add in the config
     * @param string $key The key of the value you want to add in the config file!
     * @param string $value The value you want to add in the config file!
     *
     * @return bool
     */
    public static function add(string $category, string $key, string $value): bool
    {
        try {
            if (!file_exists(self::$configpath)) {
                die("Config file not found!");
            }
            if (!is_writable(self::$configpath)) {
                die("We have no access to the config file!");
            } else {
                $config = new ConfigHelper(self::$configpath);
            }
        } catch (Exception $e) {
            die("Failed to init the config class! \n" . $e->__toString());
        }
        return $config->add($category, $key, $value);
    }

    /**
     * DEPRECATED: Use Settings class instead!!
     * DEPRECATED: This class is used for the settings.json file!
     *
     * Remove a value from the config file!
     *
     * @param string $category The category of the value you want to remove from the config
     * @param string $key The key of the value you want to remove from the config file!
     *
     * @return bool
     */
    public static function remove(string $category, string $key): bool
    {
        try {
            if (!file_exists(self::$configpath)) {
                die("Config file not found!");
            }
            if (!is_writable(self::$configpath)) {
                die("We have no access to the config file!");
            } else {
                $config = new ConfigHelper(self::$configpath);
            }
        } catch (Exception $e) {
            die("Failed to init the config class! \n" . $e->__toString());
        }
        return $config->remove($category, $key);
    }
}
