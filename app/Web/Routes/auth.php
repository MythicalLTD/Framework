<?php

use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\Managers\LanguageManager;

global $router;

$router->get('/auth/register', function () {
    /*
     * The requirement for each template
     */
    $lang = LanguageManager::getLang();
    session_start();

    $csrf = new MythicalSystems\Utils\CSRFHandler();

    global $renderer;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('register_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());

        exit($renderer->render('auth/register.twig'));
    }
});
