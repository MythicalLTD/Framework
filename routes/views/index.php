<?php 

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;
use MythicalSystemsFramework\Kernel\ErrorHandler as err; 

$router->add('/', function() {
    global $renderer;

    $renderer->display("index.html");
});