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

global $router;

$router->add('/errors/404', function (): void {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(404);
    exit($renderer->render('/errors/404.twig'));
});

$router->add('/errors/500', function (): void {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(500);
    exit($renderer->render('/errors/500.twig'));
});

$router->add('/errors/403', function (): void {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(403);
    exit($renderer->render('/errors/403.twig'));
});

$router->add('/errors/401', function (): void {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(401);
    exit($renderer->render('/errors/401.twig'));
});
