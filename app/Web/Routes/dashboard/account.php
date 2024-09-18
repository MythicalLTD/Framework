<?php

use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\Mail\MailBox;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;

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

global $router, $event;

$router->add('/account/api', function() : void {
    $template_name = 'account/api.twig';
    $template_array = ["sidebar_account_api_key" => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Engine::registerAlerts($renderer, $template_name);;

        exit($renderer->render($template_name, $template_array));
    }

});

$router->add('/account/security', function (): void {
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    $template_name = 'account/security.twig';
    $template_array = ["sidebar_account_security" => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('account_security'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
        Engine::registerAlerts($renderer, $template_name);;

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

$router->add('/account/settings', function () {

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $template_name = 'account/settings.twig';
    $template_array = ["sidebar_account_settings" => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('account_settings'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
        Engine::registerAlerts($renderer, $template_name);;

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

$router->add('/account/activities', function() {
    
        session_start();
        $csrf = new MythicalSystems\Utils\CSRFHandler();
    
        $template_name = 'account/activities.twig';
        $template_array = ["sidebar_account_activities" => true];
    
        global $renderer;
        if (isset($_COOKIE['token']) === false) {
            exit(header('location: /auth/login'));
        }
        $user = new UserHelper($_COOKIE['token']);
        UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            Engine::registerAlerts($renderer, $template_name);;

            $activities = UserActivity::getActivities(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false));
            $renderer->addGlobal('activities', $activities);
            exit($renderer->render($template_name, $template_array));
        }
});

$router->add('/account/mails', function () {

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    $template_name = 'account/mails.twig';
    $template_array = ["sidebar_account_mails" => true];

    global $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Engine::registerAlerts($renderer, $template_name);;
        $emails = MailBox::getEmails(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false));
        $renderer->addGlobal('emails', $emails);
        exit($renderer->render($template_name, $template_array));
    }
});
