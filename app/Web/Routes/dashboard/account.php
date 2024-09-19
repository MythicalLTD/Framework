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

use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\Mail\MailBox;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\Api\UserApi;

global $router, $event;


$router->add('/account/api/(.*)/delete', function (int $key_id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($key_id =="") {
        exit(header('location: /account/api'));
    }

    if (UserApi::doesUserOwnKey($_COOKIE['token'], $key_id)) {

        header('location: /account/api?s=deleted');
    } else {
        header('location: /account/api?e=not_owner');
    }
});

$router->add('/account/api', function (): void {
    $template_name = 'account/api.twig';
    $template_array = ['sidebar_account_api_key' => true];
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    global $renderer;
    $renderer->addGlobal('csrf_input', $csrf->input('api_form'));

    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $api_key = UserApi::getAll($_COOKIE['token']);
        $renderer->addGlobal('api_keys', $api_key);
        Engine::registerAlerts($renderer, $template_name);
        exit($renderer->render($template_name, $template_array));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['apiAccess']) && !$_POST['apiAccess'] == "") {
            $access = $_POST['apiAccess'];
        } else {
            $access = 'r';
        }
        if (isset($_POST['apiKey']) && !$_POST['apiKey'] == "") {
            $randomName = $_POST["apiKey"];
        } else {
            $randomName = bin2hex(random_bytes(8));
        }
        $token = $_COOKIE['token'];
        if (!$csrf->validate('api_form')) {
            header('Location: /account/api?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            UserApi::add($randomName, $token, UserApi::generateKey(), $access);
            header('location: /account/api?s=added');
        } else {
            exit(header('Location: /account/api?e=captcha'));
        }
    }
});

$router->add('/account/security', function (): void {
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    $template_name = 'account/security.twig';
    $template_array = ['sidebar_account_security' => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('account_security'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name, $template_array));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('account_security')) {
            header('Location: /account/security?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
        }
        header('Location: /account/security?e=captcha');
        exit;
    }
});

$router->add('/account/settings', function (): void {

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $template_name = 'account/settings.twig';
    $template_array = ['sidebar_account_settings' => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('account_settings'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name, $template_array));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('account_settings')) {
            header('Location: /account/settings?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
        }
        header('Location: /account/settings?e=captcha');
        exit;
    }
});

$router->add('/account/activities', function (): void {

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    $template_name = 'account/activities.twig';
    $template_array = ['sidebar_account_activities' => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Engine::registerAlerts($renderer, $template_name);

        $activities = UserActivity::getActivities(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false));
        $renderer->addGlobal('activities', $activities);
        exit($renderer->render($template_name, $template_array));
    }
});

$router->add('/account/mails', function (): void {

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    $template_name = 'account/mails.twig';
    $template_array = ['sidebar_account_mails' => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Engine::registerAlerts($renderer, $template_name);
        $emails = MailBox::getEmails(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false));
        $renderer->addGlobal('emails', $emails);
        exit($renderer->render($template_name, $template_array));
    }
});
