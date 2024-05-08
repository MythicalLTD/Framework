<?php 

use MythicalSystemsFramework\Managers\ConfigManager as cfg;

$router->add('/', function() {
    global $renderer;

    $renderer->display("index.html");
});