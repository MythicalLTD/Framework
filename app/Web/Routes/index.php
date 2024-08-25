<?php

global $router;

$router->add('/', function () {
    /*
     * The requirement for each template
     */
    global $renderer;
    exit(header('location: /dashboard'));
});
