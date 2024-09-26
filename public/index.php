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

try {
    if (file_exists('../storage/caches/vendor/autoload.php')) {
        require '../storage/caches/vendor/autoload.php';
    } else {
        exit('Hello, it looks like you did not run: "<code>composer install --no-dev --optimize-autoloader</code>". Please run that and refresh the page');
    }
} catch (Exception $e) {
    exit('Hello, it looks like you did not run: <code>composer install --no-dev --optimize-autoloader</code> Please run that and refresh');
}

use MythicalSystemsFramework\App;
use MythicalSystemsFramework\Api\Api as api;
use MythicalSystemsFramework\Plugins\PluginEvent;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\Plugins\PluginsManager;
use MythicalSystemsFramework\Web\Installer\Installer;

$languageManager = new MythicalSystemsFramework\Language\Manager();

$router = new Router\Router();
$event = new PluginEvent();

global $event, $languageManager;

ini_set('expose_php', 'off');
header_remove('X-Powered-By');
header_remove('Server');
/*
 * Check if the app is installed
 */
Installer::Installed($router);
/*
 * Check if the app is healthy and all requirements are met
 */
App::checkIfAppIsHealthy();

/*
 * Start the plugin loader
 */
PluginsManager::init($event);

/**
 * Get the renderer :).
 */
$renderer = Engine::getRenderer();

/*
 * Load the routes.
 */
api::registerApiRoutes($router);
App::registerRoutes($renderer);
$event->emit('app.onAppLoad', [$router, $renderer]);

$router->add('/(.*)', function (): void {
    global $renderer;
    $renderer->addGlobal('page_name', '404');
    http_response_code(404);
    echo $renderer->render('/errors/404.twig');
});

try {
    $router->route();
} catch (Exception $e) {
    exit('Failed to start app: ' . $e->getMessage());
}
