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

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Encryption\XChaCha20;
use MythicalSystemsFramework\Managers\ConfigManager;
use MythicalSystemsFramework\Managers\Settings as SettingsHandler;
use MythicalSystemsFramework\Managers\DBSettingsManager as settings;

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

if (!isset($app_name) || $app_name === '') {
    exit('Missing APP Name');
}

if (!isset($app_timezone) || $app_timezone === '') {
    exit('Missing Timezone');
}
if (!isset($settings_app_seo_title) || $settings_app_seo_title === '') {
    exit('Missing SEO Title');
}

if (!isset($settings_app_seo_description) || $settings_app_seo_description === '') {
    exit('Missing SEO Description');
}

if (!isset($settings_app_seo_keywords) || $settings_app_seo_keywords === '') {
    exit('Missing SEO Keywords');
}

if (!isset($settings_app_logo) || $settings_app_logo === '') {
    exit('Missing APP Logo');
}

if (!in_array($app_timezone, timezone_identifiers_list())) {
    exit('Invalid Timezone! Please check the timezone list.');
}

try {
    ConfigManager::set('database', 'host', $mysql_host);
    ConfigManager::set('database', 'port', $mysql_port);
    ConfigManager::set('database', 'username', $mysql_username);
    ConfigManager::set('database', 'password', $mysql_password);
    ConfigManager::set('database', 'name', $mysql_name);
    XChaCha20::generateKey();
    try {
        try {
            MySQL::migrate();
        } catch (Exception $e) {
            exit('Failed to migrate the database: ' . $e->getMessage());
        }
        SettingsHandler::migrate(true);
        settings::update('app', 'name', $app_name);
        settings::update('app', 'timezone', $app_timezone);
        settings::update('seo', 'title', $settings_app_seo_title);
        settings::update('seo', 'description', $settings_app_seo_description);
        settings::update('seo', 'keywords', $settings_app_seo_keywords);
        settings::update('app', 'logo', $settings_app_logo);
        try {
            unlink(__DIR__ . '/../../../storage/FIRST_INSTALL');
            exit('OK_DEL_FIRST_INSTALL');
        } catch (Exception $e) {
            exit('OK_DEL_FIRST_INSTALL');
        }
    } catch (Exception $e) {
        exit('Failed to migrate the database: ' . $e->getMessage());
    }
} catch (Exception $e) {
    exit('Failed to configure the database: ' . $e->getMessage());
}
