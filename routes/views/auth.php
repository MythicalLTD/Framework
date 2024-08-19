<?php

use MythicalSystemsFramework\CloudFlare\TurnStile;

global $router;

$router->add('/auth/register', function () {
    /*
     * The requirement for each template
     */
    global $renderer;
    $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
    exit($renderer->render('auth/register.twig'));
});
