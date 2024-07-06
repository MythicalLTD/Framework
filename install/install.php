<?php

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager;
use MythicalSystemsFramework\Managers\SettingsManager as settings;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(null);

$app_name = $_GET['app_name'];
$app_timezone = $_GET['app_timezone'];
$settings_app_seo_title = $app_name;
$settings_app_seo_description = $_GET['app_seo_description'];
$settings_app_seo_keywords = $_GET['app_seo_keywords'];
$settings_app_logo = $_GET['app_logo'];
$mysql_params = $_GET['mysql'];
parse_str($mysql_params, $mysql);

$mysql_host = $mysql['host'];
$mysql_port = $mysql['port'];
$mysql_username = $mysql['username'];
$mysql_password = $mysql['password'];
$mysql_name = $mysql['name'];

if (!isset($app_name) || $app_name === "") {
    die("Missing APP Name");
}

if (!isset($app_timezone) || $app_timezone === "") {
    die("Missing Timezone");
}
if (!isset($settings_app_seo_title) || $settings_app_seo_title === "") {
    die("Missing SEO Title");
}

if (!isset($settings_app_seo_description) || $settings_app_seo_description === "") {
    die("Missing SEO Description");
}

if (!isset($settings_app_seo_keywords) || $settings_app_seo_keywords === "") {
    die("Missing SEO Keywords");
}

if (!isset($settings_app_logo) || $settings_app_logo === "") {
    die("Missing APP Logo");
}

if (!in_array($app_timezone, timezone_identifiers_list())) {
    die("Invalid Timezone! Please check the timezone list.");
}


try {
    ConfigManager::set('database', 'host', $mysql_host);
    ConfigManager::set('database', 'port', $mysql_port);
    ConfigManager::set('database', 'username', $mysql_username);
    ConfigManager::set('database', 'password', $mysql_password);
    ConfigManager::set('database', 'name', $mysql_name);
    ConfigManager::set('encryption', 'key', generateKey());
    try {
        migrateDB();
        migrateCfg();
        settings::update('app', 'name', $app_name);
        settings::update('app', 'timezone', $app_timezone);
        settings::update('seo', 'title', $settings_app_seo_title);
        settings::update('seo', 'description', $settings_app_seo_description);
        settings::update('seo', 'keywords', $settings_app_seo_keywords);
        settings::update('app', 'logo', $settings_app_logo);
        try {
            unlink(__DIR__ . '/../../FIRST_INSTALL');
            die("OK_DEL_FIRST_INSTALL");
        } catch (Exception $e) {
            die("OK_DEL_FIRST_INSTALL");
        }
    } catch (Exception $e) {
        die("Failed to migrate the database: " . $e->getMessage());
    }
} catch (Exception $e) {
    die("Failed to configure the database: " . $e->getMessage());
}

/**
 * Install functions
 * 
 * Do not touch those functions please
 * 
 * If you touch you gay
 */
function migrateDB()
{
    try {
        $mysql = new MySQL();
        $db = $mysql->connectPDO();

        $db->exec("
            CREATE TABLE IF NOT EXISTS framework_migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                script VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");

        $sqlFiles = glob(__DIR__ . '/../../migrate/database/*.sql');

        if (count($sqlFiles) > 0) {
            usort($sqlFiles, function($a, $b) {
                $aDate = strtotime(basename($a, '.sql'));
                $bDate = strtotime(basename($b, '.sql'));
                return $aDate - $bDate;
            });

            foreach ($sqlFiles as $sqlFile) {
                $script = file_get_contents($sqlFile);

                $fileName = basename($sqlFile);

                $stmt = $db->prepare("SELECT COUNT(*) FROM framework_migrations WHERE script = ?");
                $stmt->execute([$fileName]);
                $count = $stmt->fetchColumn();

                if ($count == 0) {
                    $db->exec($script);

                    $stmt = $db->prepare("INSERT INTO framework_migrations (script) VALUES (?)");
                    $stmt->execute([$fileName]);
                } else {
                    return;
                }
            }
        } else {
            die("No migrations found!");
        }
    } catch (PDOException $e) {
        die("Failed to migrate the database: " . $e->getMessage() . "");
    }
}
function migrateCfg()
{
    try {
        $mysql = new MySQL();
        $db = $mysql->connectPDO();
        $db->exec("CREATE TABLE IF NOT EXISTS `framework_settings_migrations` (`id` INT NOT NULL AUTO_INCREMENT , `script` TEXT NOT NULL , `executed_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;             ALTER TABLE `framework_settings_migrations` CHANGE `executed_at` `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;");
        $phpFiles = glob(__DIR__ . '/../../migrate/config/*.php');

        if (count($phpFiles) > 0) {
            sort($phpFiles);

            foreach ($phpFiles as $phpFile) {
                $fileName = basename($phpFile);

                $stmt = $db->prepare("SELECT COUNT(*) FROM framework_settings_migrations WHERE script = ?");
                $stmt->execute([$fileName]);
                $count = $stmt->fetchColumn();

                if ($count == 0) {
                    include $phpFile;

                    $stmt = $db->prepare("INSERT INTO framework_settings_migrations (script) VALUES (?)");
                    $stmt->execute([$fileName]);
                }
            }
        } else {
            die("No migrations found!");
        }
    } catch (PDOException $e) {
        die("Failed to migrate the database: " . $e->getMessage() . "");
    }
}



function generateKey(): string
{
    $key = "mythicalcore_" . bin2hex(random_bytes(64 * 32));
    return $key;
}
