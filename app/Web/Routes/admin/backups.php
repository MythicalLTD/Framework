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

use MythicalSystemsFramework\Backup\Backup;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;

global $router;

$router->add('/admin/backups', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/backups/list.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.backups.view')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.backups.create')
        || !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.backups.delete')
    ) {
        exit(header('location: /errors/403'));
    }

    $backups = Backup::getBackups();
    $renderer->addGlobal('backups', $backups);
    $renderer->addGlobal('page_name', 'Backups');

    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});

$router->add('/admin/backups/(.*)/restore', function ($id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.backups.restore')
    ) {
        exit(header('location: /errors/403'));
    }

    if (Backup::doesBackupExist($id) == false) {
        exit(header('location: /admin/backups?s=not_found'));
    }
    Backup::restore($id);
    UserActivity::addActivity($uuid, "Restored a backup with the ID: (" . $id . ")", CloudFlare::getRealUserIP(), "backup:restore");
    exit(header('location: /admin/backups?s=ok'));
});

$router->add('/admin/backups/(.*)/delete', function ($id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.backups.delete')
    ) {
        exit(header('location: /errors/403'));
    }
    if (Backup::doesBackupExist($id) == false) {
        exit(header('location: /admin/backups?s=not_found'));
    }
    Backup::remove($id);
    UserActivity::addActivity($uuid, "Deleted a backup with the ID: (" . $id . ")", CloudFlare::getRealUserIP(), "backup:delete");
    exit(header('location: /admin/backups?s=ok'));
});

$router->add('/admin/backups/create', function (): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.backups.create')
    ) {
        exit(header('location: /errors/403'));
    }

    $backup = Backup::take();
    Backup::setBackupStatus($backup, MythicalSystemsFramework\Backup\Status::DONE);
    UserActivity::addActivity($uuid, "Created a backup with the ID: (" . $backup . ")", CloudFlare::getRealUserIP(), "backup:create");
    exit(header('location: /admin/backups?s=ok'));
});
