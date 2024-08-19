<?php

global $router;

$router->add('/api/user/doesinfoexist', function () {
    include __DIR__ . '/../../api/User/infoalreadyexists.php';
    exit;
});

$router->add('/api/user/register', function () {
    include __DIR__ . '/../../api/User/register.php';
    exit;
});
