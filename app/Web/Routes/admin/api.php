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
use MythicalSystemsFramework\User\Announcement\Announcements;

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

    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});