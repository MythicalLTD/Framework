<?php

$router->add('/api/system/logs', function () {
    include(__DIR__ . '/../../api/System/logs.php');
});
