<?php

global $router;

$router->add('/api/system/logs', function () {
    include __DIR__ . '/../../api/System/logs.php';
});

$router->add('/api/system/getTranslation', function () {
    include __DIR__ . '/../../api/System/getTranslationKey.php';
});
