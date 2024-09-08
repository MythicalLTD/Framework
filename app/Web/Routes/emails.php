<?php

global $router;

$router->add('/emails/welcome', function () {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/welcome.html');
    echo die($file);
});


$router->add('/emails/reset-password', function () {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/reset-password.html');
    echo die($file);
});

$router->add('/emails/user-banned', function () {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/user-banned.html');
    echo die($file);
});


$router->add('/emails/login', function () {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/login.html');
    echo die($file);
});

$router->add('/emails/notification', function () {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/notification.html');
    echo die($file);
});