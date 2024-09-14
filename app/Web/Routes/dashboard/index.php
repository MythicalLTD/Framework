<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\Managers\LanguageManager;

global $router, $event;

$router->add('/dashboard', function (): void {
    global $router, $event, $renderer;
    define('TEMPLATE_NAME', 'index.twig');
    /*
     * The requirement for each template
     */
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    $lang = LanguageManager::getLang();

    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (isset($_GET['e']) && !$_GET['e'] == '') {
        $e = $_GET['e'];
        switch ($e) {
            case '2fa_already_setup':
                exit($renderer->render(TEMPLATE_NAME, ['alert_error_title' => $lang['alert_title_error'], 'alert_error_message' => $lang['alert_2fa_already_setup']]));
            case '2fa_not_setup':
                exit($renderer->render(TEMPLATE_NAME, ['alert_error_title' => $lang['alert_title_error'], 'alert_error_message' => $lang['alert_2fa_not_setup']]));

        }
    }
    if (isset($_GET['s']) && !$_GET['s'] == '') {
        $s = $_GET['s'];
        switch ($s) {
            case '2fa_setup_success':
                exit($renderer->render(TEMPLATE_NAME, ['alert_success_title' => $lang['alert_title_success'], 'alert_success_message' => $lang['alert_2fa_setup']]));
            case '2fa_not_setup':
                break;
        }
    }

    exit($renderer->render(TEMPLATE_NAME));

});
