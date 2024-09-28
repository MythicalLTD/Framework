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

use MythicalSystemsFramework\User\Activity\UserActivity;
use Twig\TwigFunction;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\Roles\RolesDataHandler;

global $router;

$router->add('/admin/users/(.*)/edit', function ($uuid): void {
    global $router, $event, $renderer;
    $template = 'admin/users/edit.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.users.view')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.users.edit')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.users.delete')

    ) {
        exit(header('location: /errors/403'));
    }

    if ($uuid == "") {
        exit(header('location: /admin/users'));
    }
    if (UserDataHandler::doesUUIDExist($uuid) === false) {
        exit(header('location: /admin/users?e=not_found'));
    }
    $userToken = UserDataHandler::getTokenUUID($uuid);
    if ($userToken === null) {
        exit(header('location: /admin/users?e=not_found'));
    }

    $renderer->addFunction(new TwigFunction('other_info', function ($info, $encrypted) use ($userToken) {
        return UserDataHandler::getSpecificUserData($userToken, $info, $encrypted);
    }));
    $activity = UserActivity::getActivities($uuid);

    $renderer->addGlobal('user_activity', $activity);
    $renderer->addGlobal('page_name', 'Users');
    $renderer->addGlobal('other_user_token', $userToken);
    $renderer->addFunction(new TwigFunction('getRoleName', function ($role) {
        return RolesDataHandler::getSpecificRoleInfo($role, 'name');
    }));

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Engine::registerAlerts($renderer, $template);
        exit($renderer->render($template));
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    } else {
        exit(header('location: /dashboard'));
    }
});

$router->add('/admin/users', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/users/list.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.users.view')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.users.edit')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.users.delete')

    ) {
        exit(header('location: /errors/403'));
    }

    $renderer->addGlobal('users', UserDataHandler::getAll());
    $renderer->addGlobal('page_name', 'Users');

    $renderer->addFunction(new TwigFunction('getRoleName', function ($role) {
        return RolesDataHandler::getSpecificRoleInfo($role, 'name');
    }));

    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});
