<?php
try {
    if (file_exists('../vendor/autoload.php')) {
        require ('../vendor/autoload.php');
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
$renderer = new Smarty();
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

$routesAPIDirectory = __DIR__ . '/../routes/api/';
$iterator2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesAPIDirectory));
$phpApiFiles = new RegexIterator($iterator2, '/\.php$/');

foreach ($phpApiFiles as $phpApiFile) {
    try {
        include $phpApiFile->getPathname();
    } catch (Exception $ex) {
        api::init();
        rsp::InternalServerError($e->getMessage());
    }
}

$router->add('/api/(.*)', function () {
    api::init();
    rsp::NotFound("The api route does not exist!");
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
    die ("Route not found!");
});

try {
    $router->route();
} catch (Exception $e) {
    die('Failed to start app: ' . $e->getMessage());
}