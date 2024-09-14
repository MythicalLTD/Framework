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

global $router, $event;

$router->add('/dashboard', function (): void {
    global $router, $event, $renderer;

    /*
     * The requirement for each template
     */
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    exit($renderer->render('index.twig'));

});
