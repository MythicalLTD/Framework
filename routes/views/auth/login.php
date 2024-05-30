<?php

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;
use MythicalSystemsFramework\Kernel\ErrorHandler as err;
use MythicalSystems\Api\Api as api;

$router->add('/auth/login', function () {
    global $renderer;

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['e']) && !$_GET['e'] == "") {
            $error = $_GET['e'];   
            $renderer->assign("ERROR", $error);
        }
        $renderer->display("auth/login.html");
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['email'])) {

        } else {
            header('location: /auth/login?e=EMAIL_MISSING');
            die();
        }
    }
});