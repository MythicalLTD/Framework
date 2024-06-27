<?php
use MythicalSystemsFramework\Managers\SettingsManager;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    if (file_exists('../vendor/autoload.php')) {
        require('../vendor/autoload.php');
    } else {
        die('Hello, it looks like you did not run: "<code>composer install --no-dev --optimize-autoloader</code>". Please run that and refresh the page');
    }
} catch (Exception $e) {
    die('Hello, it looks like you did not run: <code>composer install --no-dev --optimize-autoloader</code> Please run that and refresh');
}

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use Smarty\Smarty;
use MythicalSystems\Api\ResponseHandler as rsp;
use MythicalSystems\Api\Api as api;
$router = new \Router\Router();

if (file_exists(__DIR__ . '/../FIRST_INSTALL')) {
    $router->add('/', function () {
        include(__DIR__ . '/../core/install/index.php');
    });


    $router->add('/mysql', function () {
        include(__DIR__ . '/../core/install/mysql.php');
    });

    $router->add('/install', function () {
        include(__DIR__ . '/../core/install/install.php');
    });

    $router->add('/(.*)', function () {
        header('location: /');
    });
    $router->route();
    die();
}
die(SettingsManager::set('app', 'name'));
$renderer = new Smarty();

if (cfg::get("encryption", "key") == "") {
    die("We are sorry but you are missing the encryption key!");
}

if (!is_writable(__DIR__)) {
    die("We have no access to the framework directory!");
}
date_default_timezone_set(cfg::get('app', 'timezone'));
define('DIR_TEMPLATE', __DIR__ . '/../themes/' . cfg::get('app', 'theme'));
define('DIR_CACHE', __DIR__ . '/../caches/template');
define('DIR_COMPILE', __DIR__ . '/../caches/compile');
define('DIR_CONFIG', __DIR__ . '/../caches/config');

$renderer->setTemplateDir(DIR_TEMPLATE);
$renderer->setCacheDir(DIR_CACHE);
$renderer->setCompileDir(DIR_COMPILE);
$renderer->setConfigDir(DIR_CONFIG);
$renderer->setEscapeHtml(true);
$renderer->setCompileCheck(true);
$renderer->setCacheLifetime(3600);
$renderer->assign(
    [
        "cfg_app_name" => cfg::get("app", "name"),
        "cfg_app_logo" => cfg::get("app", "logo"),
        "cfg_app_maintenance" => cfg::get("app", "maintenance"),
        "cfg_app_theme" => cfg::get("app", "version"),
        "cfg_app_lang" => cfg::get("app", "lang"),
        "cfg_app_timezone" => cfg::get("app", "timezone"),

        "cfg_seo_title" => cfg::get("seo", "title"),
        "cfg_seo_description" => cfg::get("seo", "description"),
        "cfg_seo_keywords" => cfg::get("seo", "keywords"),

        "cfg_framework_version" => cfg::get("framework", "version"),
        "cfg_framework_branch" => cfg::get("framework", "branch"),
        "cfg_framework_name" => cfg::get("framework", "name"),
        "cfg_framework_debug" => cfg::get("framework", "debug"),
    ]
);

$routesAPIDirectory = __DIR__ . '/../routes/api/';
$iterator2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesAPIDirectory));
$phpApiFiles = new RegexIterator($iterator2, '/\.php$/');

foreach ($phpApiFiles as $phpApiFile) {
    try {
        include $phpApiFile->getPathname();
    } catch (Exception $ex) {
        api::init();
        rsp::InternalServerError($e->getMessage(), null);
    }
}

$router->add('/api/(.*)', function () {
    api::init();
    rsp::NotFound("The api route does not exist!", null);
});

$routesViewDirectory = __DIR__ . '/../routes/views/';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesViewDirectory));
$phpViewFiles = new RegexIterator($iterator, '/\.php$/');

foreach ($phpViewFiles as $phpViewFile) {
    try {
        http_response_code(200);
        include $phpViewFile->getPathname();
    } catch (Exception $ex) {
        http_response_code(500);
        die('Failed to start app: ' . $ex->getMessage());
    }
}

$router->add('/(.*)', function () {
    die("Route not found!");
});

try {
    $router->route();
} catch (Exception $e) {
    die('Failed to start app: ' . $e->getMessage());
}
