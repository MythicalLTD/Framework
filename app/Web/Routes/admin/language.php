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

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Language\Manager;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;

global $router;

$router->add('/admin/language', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/lang/list.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.languages.view')
    ) {
        exit(header('location: /errors/403'));
    }

    $langs = new Manager();
    $lang_array = $langs->getLangs();
    $renderer->addGlobal('langs', $lang_array);
    $renderer->addGlobal('page_name', 'Languages');

    Engine::registerAlerts($renderer, $template);
    exit($renderer->render($template));
});

$router->add('/admin/language/(.*)/editor', function ($lang): void {
    global $router, $event, $renderer;
    $template = 'admin/lang/editor.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.languages.edit')
    ) {
        exit(header('location: /errors/403'));
    }
    $lang_sys = new Manager();
    $file = $lang_sys->lang_dir . '/' . $lang;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $content = file_get_contents($file);
        $renderer->addGlobal('content', $content);
        exit($renderer->render($template));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        Api::init();
        Api::allowOnlyPOST();
        if (isset($_POST['content']) && !empty($_POST['content'])) {
            file_put_contents($file, $_POST['content']);
            Api::OK('Language file updated successfully.', null);
        } else {
            Api::InternalServerError('Language file could not be updated.', null);
        }

    } else {
        exit(header('location: /errors/404'));
    }
});
