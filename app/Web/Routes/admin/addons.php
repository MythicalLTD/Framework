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

use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\User\Activity\UserActivity;

global $router;

$router->add('/admin/plugins/upload', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/plugins/upload.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.install')
    ) {
        exit(header('location: /errors/403'));
    }

    $renderer->addGlobal('page_name', 'Upload Plugin');
    Engine::registerAlerts($renderer, $template);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['pluginFile']) && $_FILES['pluginFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['pluginFile']['tmp_name'];
            $fileName = $_FILES['pluginFile']['name'];
            $fileSize = $_FILES['pluginFile']['size'];
            $fileType = $_FILES['pluginFile']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            if ($fileExtension === 'mfa') {
                // Process the file upload (e.g., move to the desired directory)
                $uploadFileDir = '/path/to/upload/directory/';
                $dest_path = $uploadFileDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    exit(header('location: /admin/plugins?s=ok'));
                } else {
                    exit(header('location: /admin/plugins?s=error'));
                }
            } else {
                $renderer->addGlobal('error', 'Invalid file type. Only .mfa files are allowed.');
            }
        } else {
            $renderer->addGlobal('error', 'There was an error with the file upload.');
        }
    }

    exit($renderer->render($template));
});

$router->add('/admin/plugins', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/plugins/list.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.view')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.enable')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.install')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.uninstall')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.disable')
    ) {
        exit(header('location: /errors/403'));
    }

    $plugins = MythicalSystemsFramework\Plugins\Database::getAllPlugins();
    $renderer->addGlobal('plugins', $plugins);
    $renderer->addGlobal('page_name', 'Plugins');
    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});

$router->add('/admin/plugins/(.*)/disable', function (string $id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.disable')
    ) {
        exit(header('location: /errors/403'));
    }

    if (MythicalSystemsFramework\Plugins\Database::doesPluginExistID($id) == false) {
        exit(header('location: /admin/plugins?s=not_found'));
    }
    $plugin_name = MythicalSystemsFramework\Plugins\Database::getPluginNameById($id);
    UserActivity::addActivity($uuid, 'Disabled the plugin: (' . $plugin_name . ')', CloudFlare::getRealUserIP(), 'plugin:disabled');
    MythicalSystemsFramework\Plugins\PluginsManager::disablePlugin($plugin_name);
    exit(header('location: /admin/plugins?s=ok'));

});

$router->add('/admin/plugins/(.*)/enable', function (string $id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.plugins.enable')
    ) {
        exit(header('location: /errors/403'));
    }

    if (MythicalSystemsFramework\Plugins\Database::doesPluginExistID($id) == false) {
        exit(header('location: /admin/plugins?s=not_found'));
    }
    $plugin_name = MythicalSystemsFramework\Plugins\Database::getPluginNameById($id);

    MythicalSystemsFramework\Plugins\Database::updatePlugin($plugin_name, 'enabled', 'true');
    UserActivity::addActivity($uuid, 'Enabled the plugin: (' . $plugin_name . ')', CloudFlare::getRealUserIP(), 'plugin:enabled');
    exit(header('location: /admin/plugins?s=ok'));

});

$router->add('/admin/plugin/(.*)/info', function (string $id): void {});
