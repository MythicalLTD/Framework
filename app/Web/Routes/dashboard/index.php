<?php

use MythicalSystemsFramework\User\UserHelper;

global $router, $event;

$router->add('/dashboard', function () {
    global $event;

    /*
     * The requirement for each template
     */
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    } else {
        $user = new UserHelper($_COOKIE['token']);
        global $renderer;
        exit($renderer->render('index.twig'));
    }
});
