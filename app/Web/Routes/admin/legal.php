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

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;

global $router;

$router->add('/admin/legal', function (): void {
    global $router, $event, $renderer;
    $template = 'admin/legal.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    $renderer->addGlobal('page_name', 'Legal');

    Engine::registerAlerts($renderer, $template);
    $content = file_get_contents(__DIR__.'/../../../LEGAL_STUFF.md');

    $renderer->addGlobal('content', $content);

    
    exit($renderer->render($template));
});
