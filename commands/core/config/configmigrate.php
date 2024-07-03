<?php

use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;

class configmigrateCommand
{
    public function execute()
    {
        try {
            $mysql = new MySQL();
            $db = $mysql->connectPDO();
            $db->exec("CREATE TABLE IF NOT EXISTS `framework_settings_migrations` (`id` INT NOT NULL AUTO_INCREMENT , `script` TEXT NOT NULL , `executed_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;             ALTER TABLE `framework_settings_migrations` CHANGE `executed_at` `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;");
            $phpFiles = glob(__DIR__ . '/../../migrate/config/*.php');

            if (count($phpFiles) > 0) {
                sort($phpFiles);

                $migratedCount = 0; // Initialize migrated count

                foreach ($phpFiles as $phpFile) {
                    $fileName = basename($phpFile);

                    $stmt = $db->prepare("SELECT COUNT(*) FROM framework_settings_migrations WHERE script = ?");
                    $stmt->execute([$fileName]);
                    $count = $stmt->fetchColumn();

                    if ($count == 0) {
                        include $phpFile;

                        $stmt = $db->prepare("INSERT INTO framework_settings_migrations (script) VALUES (?)");
                        $stmt->execute([$fileName]);

                        $migratedCount++; // Increment migrated count
                    }
                }

                echo color::translateColorsCode("&fMigration completed. Migrated &e$migratedCount &ffiles.");
            } else {
                echo color::translateColorsCode("&fMigration completed. Migrated &e0 &ffiles.");
            }
        } catch (PDOException $e) {
            echo color::translateColorsCode("Failed to migrate the database: " . $e->getMessage() . "");
        }
    }
}
