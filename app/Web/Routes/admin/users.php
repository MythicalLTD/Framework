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

use Twig\TwigFunction;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\Roles\RolesDataHandler;

global $router;

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
