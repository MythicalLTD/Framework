<?php 

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;

$router->add('/', function() {
    /**
     * The requirement for each template 
     */
    global $renderer;    

    die($renderer->render('index.twig'));
});