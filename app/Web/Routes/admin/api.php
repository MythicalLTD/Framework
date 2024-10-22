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

use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\Config;
use MythicalSystemsFramework\Encryption\XChaCha20;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Handlers\ActivityHandler;
use MythicalSystemsFramework\Managers\Settings as settings;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;

global $router;

$router->add('/admin/api', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/api/list.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.api.view')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.api.create')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.api.edit')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.api.delete')
    ) {
        exit(header('location: /errors/403'));
    }

    $renderer->addGlobal('page_name', 'API Keys');
    $renderer->addGlobal('apis', \MythicalSystemsFramework\Api\Database::getKeys());
    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});

$router->add('/admin/api/(.*)/delete', function ($key_id) : void {
    global $router, $event, $renderer;
    $template = 'admin/api/delete.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.api.delete')
    ) {
        exit(header('location: /errors/403'));
    }


    if ($key_id == null || $key_id == '' || $key_id == "0") {
        header('location: /admin/api?s=missing_fields');
        die();
    }

    if (\MythicalSystemsFramework\Api\Database::doesKeyExist($key_id) === false) {
        header('location: /admin/api?s=code_invalid');
        die();
    }
    UserActivity::addActivity($uuid, 'User deleted api key form admin area id: '.$key_id.'',CloudFlare::getRealUserIP(), 'ADMIN:API:CREATE');

    \MythicalSystemsFramework\Api\Database::deleteKey($key_id);
    header('location: /admin/api?s=done');

});

$router->add('/admin/api/create', function (): void {
    $tags = $_POST['ips'];
    $json = json_decode($tags,true);
    $taglist = [];
    foreach ($json as $tag) {
        if (isset($tag['value'])) {
            $taglist[] = $tag['value'];
        }
    }

    $tag_list = implode(',', $taglist);

    global $router, $event, $renderer;
    $template = 'admin/api/create.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.api.create')
    ) {
        exit(header('location: /errors/403'));
    }

    $renderer->addGlobal('page_name', 'Create API Key');
    if (isset($_POST['apiName']) && isset($_POST['apiAccess'])) {
        $name = $_POST['apiName'];
        $access = $_POST['apiAccess'];
        UserActivity::addActivity($uuid, 'User created api key form admin area',CloudFlare::getRealUserIP(), 'ADMIN:API:CREATE');
        \MythicalSystemsFramework\Api\Database::createKey($name, $access, $tag_list);
        header('location: /admin/api?s=created');
        die();
    } else {
        $name = $_POST['apiName'];
        $access = $_POST['apiAccess'];
        $missingFields = [];
        if ($name == null) {
            $missingFields[] = 'apiName';
        }
        if ($access == null) {
            $missingFields[] = 'apiAccess';
        }
        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, "Missing fields in the API key creation form: " . implode(', ', $missingFields));
    }
});
