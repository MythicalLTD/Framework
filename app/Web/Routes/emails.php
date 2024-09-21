<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

global $router;

$router->add('/emails/verify', function (): void {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/verify.html');
    echo exit($file);
});

$router->add('/emails/reset-password', function (): void {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/reset-password.html');
    echo exit($file);
});

$router->add('/emails/user-banned', function (): void {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/user-banned.html');
    echo exit($file);
});

$router->add('/emails/login', function (): void {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/login.html');
    echo exit($file);
});

$router->add('/emails/notification', function (): void {
    $file = file_get_contents(__DIR__ . '/../../../storage/mails/notification.html');
    echo exit($file);
});
