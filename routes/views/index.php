<?php

global $router;

$router->add('/', function () {
    /*
     * The requirement for each template
     */
    global $renderer;

    exit($renderer->render('index.twig'));
});
