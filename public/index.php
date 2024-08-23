<?php

try {
    if (file_exists('../vendor/autoload.php')) {
        require '../vendor/autoload.php';
    } else {
        exit('Hello, it looks like you did not run: "<code>composer install --no-dev --optimize-autoloader</code>". Please run that and refresh the page');
    }
} catch (Exception $e) {
    exit('Hello, it looks like you did not run: <code>composer install --no-dev --optimize-autoloader</code> Please run that and refresh');
}

use MythicalSystemsFramework\App;
use MythicalSystemsFramework\Api\Api as api;
use MythicalSystemsFramework\Plugins\PluginsManager;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\Web\Installer\Installer;

$router = new Router\Router();



/*
 * Check if the app is installed
 */
Installer::Installed($router);
/*
 * Check if the app is healthy and all requirements are met
 */
App::checkIfAppIsHealthy();

/**
 * Get the renderer :).
 */
$renderer = Engine::getRenderer();

/*
 * Load the routes.
 */
api::registerApiRoutes($router);
App::registerRoutes($renderer);


try {
    PluginsManager::init($router, $renderer);
    $router->route();
} catch (Exception $e) {
    exit('Failed to start app: ' . $e->getMessage());
}
