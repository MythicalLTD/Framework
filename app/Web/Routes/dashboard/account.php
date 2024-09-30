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
use MythicalSystemsFramework\User\Api\UserApi;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\Mail\MailBox;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\Notification\Notifications;

global $router, $event, $renderer;

$router->add('/account/notification/(.*)/read', function ($id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);
    if ($id == '') {
        exit(header('location: /dashboard'));
    }

    if (Notifications::doesUserOwnThisNotification($uuid, $id) == true) {
        Notifications::markAsRead($id, $uuid);
        exit(header('location: /dashboard'));
    }

    header('location: /dashboard?e=user_not_own_object');
    exit;

});

$router->add('/account/mails/(.*)/view', function ($mail_id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($mail_id == '') {
        exit(header('location: /account/mails'));
    }
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (MailBox::doesUserOwnThisEmail($uuid, $mail_id)) {
        $mail = MailBox::getMailContent($uuid, $mail_id);
        UserActivity::addActivity($uuid, 'Email viewed (' . $mail_id . ')', CloudFlare::getUserIP(), 'user:email:viewed');
        exit($mail);
    }
    header('location: /account/mails?e=user_not_own_object');
    exit;

});

$router->add('/account/api/(.*)/delete', function ($key_id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($key_id == '') {
        exit(header('location: /account/api'));
    }

    if (UserApi::doesUserOwnKey($_COOKIE['token'], $key_id)) {
        UserApi::remove($key_id);
        UserActivity::addActivity($key_id, 'User deleted client api key!', CloudFlare::getRealUserIP(), 'user:api:key:deleted');
        header('location: /account/api?s=deleted');
    } else {
        header('location: /account/api?e=user_not_own_object');
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

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $api_key = UserApi::getAll($_COOKIE['token']);
        $renderer->addGlobal('api_keys', $api_key);
        Engine::registerAlerts($renderer, $template_name);
        exit($renderer->render($template_name, $template_array));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['apiAccess']) && !$_POST['apiAccess'] == '') {
            $access = $_POST['apiAccess'];
        } else {
            $access = 'r';
        }
        if (isset($_POST['apiKey']) && !$_POST['apiKey'] == '') {
            $randomName = $_POST['apiKey'];
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
    $user = new UserHelper($_COOKIE['token'], $renderer);
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
            if (isset($_POST['currentPassword']) && !$_POST['currentPassword'] == '') {
                $currentPassword = $_POST['currentPassword'];
            } else {
                header('Location: /account/security?e=missing_fields');
                exit;
            }

            if (isset($_POST['newPassword']) && $_POST['newPassword'] == '') {
                header('Location: /account/security?e=missing_fields');
                exit;
            }
            $newPassword = $_POST['newPassword'];

            if (isset($_POST['confirmPassword']) && $_POST['confirmPassword'] == '') {
                header('Location: /account/security?e=missing_fields');
                exit;
            }
            $confirmPassword = $_POST['confirmPassword'];

            if ($newPassword != $confirmPassword) {
                header('Location: /account/security?e=password_mismatch');
                exit;
            }

            $currentDBPassword = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'password', true);

            if ($currentDBPassword != $currentPassword) {
                header('Location: /account/security?e=password_not_valid');
                exit;
            }

            UserDataHandler::updateSpecificUserData($_COOKIE['token'], 'password', $newPassword, true);
            $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);
            UserActivity::addActivity($uuid, 'Password changed', CloudFlare::getUserIP(), 'user:password:changed');
            header('Location: /account/security?s=updated');
            exit;
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
    $user = new UserHelper($_COOKIE['token'], $renderer);
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
            if (isset($_POST['firstName']) && !$_POST['firstName'] == '') {
                $firstName = $_POST['firstName'];
            } else {
                $firstName = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'first_name', true);
            }
            if (isset($_POST['lastName']) && !$_POST['lastName'] == '') {
                $lastName = $_POST['lastName'];
            } else {
                $lastName = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'last_name', true);
            }
            if (isset($_POST['email']) && !$_POST['email'] == '') {
                $email = $_POST['email'];
            } else {
                $email = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'email', false);
            }
            if (isset($_POST['avatar']) && !$_POST['avatar'] == '') {
                $avatar = $_POST['avatar'];
            } else {
                $avatar = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'avatar', false);
            }
            if (isset($_POST['background']) && !$_POST['background'] == '') {
                $banner = $_POST['background'];
            } else {
                $banner = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'background', false);
            }
            if ($email == UserDataHandler::getSpecificUserData($_COOKIE['token'], 'email', false)) {
            } else {
                if (UserDataHandler::doesEmailExist($email)) {
                    header('Location: /account/settings?e=email_exists');
                    exit;
                }
                UserDataHandler::updateSpecificUserData($_COOKIE['token'], 'email', $email, false);

            }

            UserDataHandler::updateSpecificUserData($_COOKIE['token'], 'first_name', $firstName, true);
            UserDataHandler::updateSpecificUserData($_COOKIE['token'], 'last_name', $lastName, true);
            UserDataHandler::updateSpecificUserData($_COOKIE['token'], 'avatar', $avatar, false);
            UserDataHandler::updateSpecificUserData($_COOKIE['token'], 'background', $banner, false);
            $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);
            UserActivity::addActivity($uuid, 'User information updated in the database!', CloudFlare::getUserIP(), 'user:profile:updated');
            header('Location: /account/settings?s=updated');
            exit;
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
    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        Engine::registerAlerts($renderer, $template_name);

        $activities = UserActivity::getActivities(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false));
        $renderer->addGlobal('activities', $activities);
        exit($renderer->render($template_name, $template_array));
    }
});

$router->add('/account/mails/(.*)/delete', function ($mail_id): void {
    global $router, $event, $renderer;
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($mail_id == '') {
        exit(header('location: /account/mails'));
    }
    $uuid = UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false);

    if (MailBox::doesUserOwnThisEmail($uuid, $mail_id)) {
        MailBox::deleteEmail($uuid, $mail_id);
        UserActivity::addActivity($uuid, 'Email deleted', CloudFlare::getUserIP(), 'user:email:deleted');
        exit(header('location: /account/mails?s=deleted'));
    }
    header('location: /account/mails?e=user_not_own_object');
    exit;

});

$router->add('/account/mails', function (): void {
    global $router, $event, $renderer;
    $template_name = 'account/mails.twig';
    $template_array = ['sidebar_account_mails' => true];
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }
    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $emails = MailBox::getEmails(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false));
        $renderer->addGlobal('emails', $emails);
        Engine::registerAlerts($renderer, $template_name);
        exit($renderer->render($template_name, $template_array));
    }
});
