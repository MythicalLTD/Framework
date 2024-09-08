<?php

namespace MythicalSystemsFramework;

use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Web\Installer\Installer;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class App extends \MythicalSystems\Main
{
    /**
     * Register the routes.
     */
    public static function registerRoutes(\Twig\Environment $renderer): void
    {
        $routesViewDirectory = __DIR__ . '/Web/Routes';
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($routesViewDirectory));
        $phpViewFiles = new \RegexIterator($iterator, '/\.php$/');

        foreach ($phpViewFiles as $phpViewFile) {
            try {
                http_response_code(200);
                include $phpViewFile->getPathname();
            } catch (\Exception $ex) {
                http_response_code(500);
                exit('Failed to start app: ' . $ex->getMessage());
            }
        }
    }

    /**
     * Call the garbage collector.
     */
    public static function callGarbageCollector(): void
    {
        gc_enable();
        gc_mem_caches();
        gc_collect_cycles();
        gc_disable();
    }

    /**
     * Check if the app is healthy.
     */
    public static function checkIfAppIsHealthy(): void
    {
        try {
            self::checkForRequirements();
        } catch (\Exception $e) {
            Installer::showError('We are sorry but the app is missing required extensions!');
        }
        try {
            if (cfg::get('encryption', 'key') == '') {
                Installer::showError('We are sorry but you are missing the encryption key!');
            }
            if (cfg::get('database', 'host') == '') {
                Installer::showError('We are sorry but you are missing the database host!');
            }
            if (cfg::get('database', 'username') == '') {
                Installer::showError('We are sorry but you are missing the database user!');
            }
            if (cfg::get('database', 'port') == '') {
                Installer::showError('We are sorry but you are missing the database port!');
            }
            if (cfg::get('database', 'name') == '') {
                Installer::showError('We are sorry but you are missing the database name!');
            }
        } catch (\Exception $e) {
            Installer::showError('We can not read the configuration file!');
        }
        try {
            $database = new MySQL();
            if ($database->tryConnection(cfg::get('database', 'host'), cfg::get('database', 'port'), cfg::get('database', 'username'), cfg::get('database', 'password'), cfg::get('database', 'name')) == false) {
                Installer::showError('We are sorry but we could not connect to the database!');
            }
        } catch (\Exception $e) {
            Installer::showError('We are sorry but we could not connect to the database!');
        }
        try {
            date_default_timezone_set(Settings::getSetting('app', 'timezone'));
        } catch (\Exception $e) {
            Installer::showError('We are sorry but we could not set the timezone!');
        }
    }

    /**
     * Check for the requirements of the app.
     */
    public static function checkForRequirements(): void
    {
        // Check if the app is healthy
        // If the app is not healthy, show a error page!

        if (!is_writable(__DIR__ . '/../')) {
            Installer::showError('We have no access to the framework directory!');
        }

        if (!is_writable(__DIR__ . '/../storage')) {
            Installer::showError('We have no access to the storage directory!');
        }

        if (!is_writable(__DIR__ . '/../storage/caches')) {
            Installer::showError('We have no access to the cache directory!');
        }

        if (!is_writable(__DIR__ . '/../storage/logs')) {
            Installer::showError('We have no access to the logs directory!');
        }

        if (!is_writable(__DIR__ . '/../storage/themes')) {
            Installer::showError('We have no access to the themes directory!');
        }

        if (!extension_loaded('mysqli')) {
            Installer::showError('MySQL extension is not installed!');
        }

        if (!extension_loaded('curl')) {
            Installer::showError('Curl extension is not installed!');
        }

        if (!extension_loaded('gd')) {
            Installer::showError('GD extension is not installed!');
        }

        if (!extension_loaded('mbstring')) {
            Installer::showError('MBString extension is not installed!');
        }

        if (!extension_loaded('openssl')) {
            Installer::showError('OpenSSL extension is not installed!');
        }

        if (!extension_loaded('zip')) {
            Installer::showError('Zip extension is not installed!');
        }

        if (!extension_loaded('bcmath')) {
            Installer::showError('Bcmath extension is not installed!');
        }

        if (!extension_loaded('json')) {
            Installer::showError('JSON extension is not installed!');
        }

        if (!extension_loaded('sodium')) {
            Installer::showError('sodium extension is not installed!');
        }

        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            Installer::showError('This application requires at least PHP 8.1.0');
        }

        $url = Settings::getSetting('app', 'url');
        if ($url == "http://example.com") {
            Settings::updateSetting('app', 'url', "https://".$_SERVER['SERVER_NAME'],true);
        }
    }
}
