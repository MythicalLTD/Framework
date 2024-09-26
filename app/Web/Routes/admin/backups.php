<?php

use MythicalSystemsFramework\Backup\Backup;
use MythicalSystemsFramework\User\Announcement\Announcements;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;

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
        !UserDataHandler::hasPermission($_COOKIE['token'], "mythicalframework.admin.backups.view") ||
        !UserDataHandler::hasPermission($_COOKIE['token'], "mythicalframework.admin.backups.create") ||
        !UserDataHandler::hasPermission($_COOKIE['token'], "mythicalframework.admin.backups.delete")
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

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], "mythicalframework.admin.backups.restore")
    ) {
        exit(header('location: /errors/403'));
    }

    if (Backup::doesBackupExist($id) == false) {
        exit(header('location: /admin/backups?s=not_found'));
    } else {
        Backup::restore($id);
        exit(header('location: /admin/backups?s=ok'));
    }
});

$router->add('/admin/backups/(.*)/delete', function ($id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], "mythicalframework.admin.backups.delete")
    ) {
        exit(header('location: /errors/403'));
    }  
    if (Backup::doesBackupExist($id) == false) {
        exit(header('location: /admin/backups?s=not_found'));
    } else {
        Backup::remove($id);
        exit(header('location: /admin/backups?s=ok'));
    }


});

$router->add('/admin/backups/create', function (): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], "mythicalframework.admin.backups.create")
    ) {
        exit(header('location: /errors/403'));
    }

    $backup = Backup::take();
    Backup::setBackupStatus($backup, \MythicalSystemsFramework\Backup\Status::DONE);

    exit(header('location: /admin/backups?s=ok'));
});