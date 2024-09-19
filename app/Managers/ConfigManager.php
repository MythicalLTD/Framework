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

namespace MythicalSystemsFramework\Managers;

use MythicalSystems\Helpers\ConfigHelper;

class ConfigManager
{
    private static string $configpath = __DIR__ . '/../../storage/settings.json';

    /**
     * DEPRECATED: Use Settings class instead!!
     * DEPRECATED: This class is used for the settings.json file!
     *
     * Get value form the config!
     *
     * @param string $category The category of the value you want to take from the config
     */
    public static function get(string $category, string $key): ?string
    {
        try {
            if (!file_exists(self::$configpath)) {
                exit('Config file not found!');
            }
            if (!is_writable(self::$configpath)) {
                exit('We have no access to the config file!');
            }

            
            $config = new ConfigHelper(self::$configpath);

        } catch (\Exception $e) {
            exit("Failed to init the config class! \n" . $e->__toString());
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
                exit('Config file not found!');
            }
            if (!is_writable(self::$configpath)) {
                exit('We have no access to the config file!');
            }
            $config = new ConfigHelper(self::$configpath);

        } catch (\Exception $e) {
            exit("Failed to init the config class! \n" . $e->__toString());
        }

        return $config->set($category, $key, $value);
    }
}
