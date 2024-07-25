<?php

$router->add('/errors/404', function () {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(404);
    exit($renderer->render('/errors/404.twig'));
});

$router->add('/errors/500', function () {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(500);
    exit($renderer->render('/errors/500.twig'));
});

$router->add('/errors/403', function () {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(403);
    exit($renderer->render('/errors/403.twig'));
});

$router->add('/errors/401', function () {
    /*
     * The requirement for each template
     */
    global $renderer;
    http_response_code(401);
    exit($renderer->render('/errors/401.twig'));
});
