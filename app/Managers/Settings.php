<?php

namespace MythicalSystemsFramework\Managers;

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQLCache;
use MythicalSystemsFramework\Managers\exception\settings\NoMigrationsFound;

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

    public static function getSetting(string $category, string $name): ?string
    {
        self::up();
        $settings_file = self::$cache_path . '/framework_settings.json';

        if (!file_exists($settings_file)) {
            MySQLCache::saveCache('framework_settings');
        }

        $settings = new \MythicalSystems\Helpers\ConfigHelper($settings_file);
        self::down();

        return $settings->get($category, $name);
    }

    /**
     * Update a setting in the database.
     *
     * @param string $category The name of the category
     * @param string $name The name of the setting
     * @param string $value The value you want to replace with!
     * @param bool $updateCache Update the cache after updating the setting
     *
     * @throws \Exception
     */
    public static function updateSetting(string $category, string $name, string $value, bool $updateCache = true): void
    {
        SettingsManager::update($category, $name, $value);
        if ($updateCache) {
            MySQLCache::saveCache('framework_settings');
        }
    }

    public static function migrate(bool $isTerminal = false): void
    {
        try {
            $mysql = new MySQL();
            $db = $mysql->connectPDO();
            $db->exec('CREATE TABLE IF NOT EXISTS `framework_settings_migrations` (`id` INT NOT NULL AUTO_INCREMENT , `script` TEXT NOT NULL , `executed_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;             ALTER TABLE `framework_settings_migrations` CHANGE `executed_at` `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;');
            $phpFiles = glob(__DIR__ . '/../../migrate/config/*.php');
            if (count($phpFiles) > 0) {
                sort($phpFiles);

                $migratedCount = 0; // Initialize migrated count

                foreach ($phpFiles as $phpFile) {
                    $fileName = basename($phpFile);

                    $stmt = $db->prepare('SELECT COUNT(*) FROM framework_settings_migrations WHERE script = ?');
                    $stmt->execute([$fileName]);
                    $count = $stmt->fetchColumn();

                    if ($count == 0) {
                        include $phpFile;

                        $stmt = $db->prepare('INSERT INTO framework_settings_migrations (script) VALUES (?)');
                        $stmt->execute([$fileName]);

                        ++$migratedCount; // Increment migrated count
                    }
                }
                if ($isTerminal) {
                    echo color::translateColorsCode('&rMigration completed. &oMigrated &e' . $migratedCount . ' &rfiles.&o');
                }
            } else {
                if ($isTerminal) {
                    echo color::translateColorsCode('&rNo migrations found!');
                }
            }
        } catch (\Exception $e) {
            if ($isTerminal) {
                echo color::translateColorsCode('&cFailed to migrate the database: &r' . $e->getMessage() . '');
            } else {
                throw new NoMigrationsFound('No migrations found!' . $e->getMessage());
            }
        }
    }
}
