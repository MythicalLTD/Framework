<?php

namespace MythicalSystemsFramework\Managers;

use MythicalSystemsFramework\Database\MySQLCache;

class Settings
{
    public static string $cache_path = __DIR__ . '/../../caches';

    public static function up(): void
    {
        if (!is_dir(self::$cache_path)) {
            mkdir(self::$cache_path, 0777, true);
        }
    }

    public static function down(): void
    {
        // No implementation needed for now
    }

    public static function getSetting(string $category, string $name): string|null
    {
        self::up();
        $settings_file = self::$cache_path . '/framework_settings.json';

        if (!file_exists($settings_file)) {
            MySQLCache::saveCache("framework_settings");
        }

        $settings = new \MythicalSystems\Helpers\ConfigHelper($settings_file);
        self::down();
        return $settings->get($category, $name);
    }
    /**
     * Update a setting in the database
     * 
     * @param string $category The name of the category
     * @param string $name The name of the setting
     * @param string $value The value you want to replace with!
     * @param bool $updateCache Update the cache after updating the setting
     * 
     * @return void
     * @throws Exception
     */
    public static function updateSetting(string $category, string $name, string $value, bool $updateCache = true) : void
    {
        SettingsManager::update($category, $name, $value);
        if ($updateCache) {
            MySQLCache::saveCache("framework_settings");
        }
    }
}
