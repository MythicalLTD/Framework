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
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\User\Announcement\Announcements;

global $router;

$router->add('/admin/announcements', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/announcements/list.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.view')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.create')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.edit')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.delete')
    ) {
        exit(header('location: /errors/403'));
    }

    $announcements = Announcements::getAllSortedById();
    $renderer->addGlobal('announcements', $announcements);
    $renderer->addGlobal('page_name', 'Announcements');

    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});

$router->add('/admin/announcements/create', function (): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);


    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.create')
    ) {
        exit(header('location: /errors/403'));
    }

    if (isset($_POST['title']) && isset($_POST['description'])) {
        Announcements::create($_POST['title'], $_POST['description']);
        exit(header('location: /admin/announcements?s=ok'));
    }
    UserActivity::addActivity($uuid, "Created an announcement", CloudFlare::getRealUserIP(), "announcement:create");
    exit(header('location: /admin/announcements?e=missing_fields'));
});

$router->add('/admin/announcements/(.*)/delete', function ($aid): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.create')
    ) {
        exit(header('location: /errors/403'));
    }
    if (Announcements::exists($aid) === false) {
        exit(header('location: /admin/announcements?s=not_found'));
    }

    Announcements::delete($aid);
    UserActivity::addActivity($uuid, "Deleted an announcement", CloudFlare::getRealUserIP(), "announcement:delete");
    exit(header('location: /admin/announcements?s=ok'));
});

$router->add('/admin/announcements/(.*)/edit', function ($aid): void {
    global $router, $event, $renderer;
    $template = 'admin/announcements/edit.twig';

    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.announcements.create')
    ) {
        exit(header('location: /errors/403'));
    }

    if (Announcements::exists($aid) === false) {
        exit(header('location: /admin/announcements?s=not_found'));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['title']) && isset($_POST['message'])) {
            Announcements::edit($aid, $_POST['title'], $_POST['message']);
            exit(header('location: /admin/announcements?s=ok'));
        }
        exit(header('location: /admin/announcements?e=missing_fields'));
    }
    $array = Announcements::getOne($aid);
    $renderer->addGlobal('announcement', $array);
    UserActivity::addActivity($uuid, "Edited an announcement", CloudFlare::getRealUserIP(), "announcement:edit");
    exit($renderer->render($template));
});
