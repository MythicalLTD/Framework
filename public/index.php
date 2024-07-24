<?php

use MythicalSystemsFramework\Kernel\Debugger;

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

use MythicalSystems\Api\ResponseHandler as rsp;
use MythicalSystems\Api\Api as api;
use MythicalSystemsFramework\Database\MySQLCache;
use MythicalSystemsFramework\Managers\SettingsManager as setting;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Managers\LanguageManager;
use MythicalSystemsFramework\Managers\Settings;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$router = new \Router\Router();


/**
 * Check if the app is installed
 */
if (file_exists(__DIR__ . '/../FIRST_INSTALL')) {
    $router->add('/', function () {
        include(__DIR__ . '/../install/index.php');
    });


    $router->add('/mysql', function () {
        include(__DIR__ . '/../install/mysql.php');
    });

    $router->add('/install', function () {
        include(__DIR__ . '/../install/install.php');
    });

    $router->add('/(.*)', function () {
        header('location: /');
    });
    $router->route();
    die();
}


if (cfg::get("encryption", "key") == "") {
    die("We are sorry but you are missing the encryption key!");
}

if (!is_writable(__DIR__)) {
    die("We have no access to the framework directory!");
}

if (!is_writable(__DIR__ . '/../caches')) {
    die("We have no access to the cache directory!");
}
date_default_timezone_set(setting::get('app', 'timezone'));
define('DIR_TEMPLATE', __DIR__ . '/../themes/' . setting::get('app', 'theme'));
define('DIR_CACHE', __DIR__ . '/../caches');
define('TIMEZONE', setting::get('app', 'timezone'));
/**
 * Load the template engine
 */

if (!is_dir(DIR_TEMPLATE)) {
    die("The theme directory does not exist!");
}

if (!is_dir(DIR_CACHE)) {
    mkdir(DIR_CACHE, 0777, true);
}
$loader = new FilesystemLoader(DIR_TEMPLATE);
$renderer = new Environment($loader, [
    'cache' => DIR_CACHE,
    'auto_reload' => true,
    'debug' => true
]);

/**
 * Add global functions to the renderer
 *
 * This will allow the renderer to get the settings and cfg values
 *
 */

/**
 * Add the settings function to the renderer
 */
$renderer->addFunction(new \Twig\TwigFunction('setting', function ($section, $key) {
    return setting::get($section, $key);
}));
/**
 * Add the cfg function to the renderer
 */
$renderer->addFunction(new \Twig\TwigFunction('cfg', function ($section, $key) {
    return cfg::get($section, $key);
}));
/**
 * Add the language function to the renderer
 */
$renderer->addFunction(new \Twig\TwigFunction('lang', function ($key) {
    $translations = LanguageManager::getLang();
    return isset($translations[$key]) ? $translations[$key] : LanguageManager::logKeyTranslationNotFound($key);
}));

$renderer->addGlobal('php_version', phpversion());
$renderer->addGlobal('page_name', "Home");
define('VIEW_ENGINE', $renderer);

/**
 * Load the routes
 */
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
    global $renderer;
    http_response_code(404);
    die($renderer->render('/errors/404.twig'));
});
try {
    $router->route();
} catch (Exception $e) {
    die('Failed to start app: ' . $e->getMessage());
}
