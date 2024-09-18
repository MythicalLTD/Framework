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

use PragmaRX\Google2FA\Google2FA;
use MythicalSystemsFramework\Mail\MailForgot;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\Mail\Templates\Login;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Mail\MailVerification;
use MythicalSystemsFramework\Mail\Templates\Forgot;
use MythicalSystemsFramework\User\TwoFactor\TwoFactor;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\Mail\Templates\Verification;
use MythicalSystemsFramework\Google\TwoFactorAuthentication;

global $router;

/**
 * Reset password.
 *
 * This route will handle the reset password.
 */
$router->add('/auth/reset-password', function (): void {
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $template_name = 'auth/reset.twig';
    global $renderer;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['token']) && !$_GET['token'] == '') {
            $token = $_GET['token'];
            if (MailForgot::isValid($token)) {
                $account_token = MailForgot::getAccountToken($token);
                $renderer->addGlobal('csrf_input', $csrf->input('forgot_form'));
                $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
                $renderer->addGlobal('skey', $token);
                Engine::registerAlerts($renderer, $template_name);

                exit($renderer->render($template_name));
            }
            header('Location: /auth/reset-password?token=' . $_GET['token'] . '&e=token_not_exist');
            exit;

        }
        header('Location: /auth/reset-password?token=' . $_GET['token'] . '&e=token_not_exist');
        exit;

    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('forgot_form')) {
            header('Location: /auth/forgot?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $token = $_POST['skey'];
            $password = $_POST['password'];

            if (MailForgot::isValid($token)) {
                $account_token = MailForgot::getAccountToken($token);
                UserDataHandler::updateSpecificUserData($account_token, 'password', $password, true);
                MailForgot::remove($token);
                UserActivity::addActivity(UserDataHandler::getSpecificUserData($account_token, 'uuid', false), 'Email password reset', CloudFlare::getUserIP(), 'user:reset-password');
                header('Location: /auth/login?s=password_reset');
                exit;
            }
            header('Location: /auth/reset-password?token=' . $_GET['token'] . '&e=token_not_exist');
            exit;

        }
        header('Location: /auth/forgot?e=captcha');
        exit;
    }
});

/*
 * Forgot password
 *
 * This route will handle the forgot password.
 */
$router->add('/auth/forgot', function (): void {
    /*
     * The requirement for each template
     */

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $template_name = 'auth/forgot.twig';
    global $renderer;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('forgot_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());

        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('forgot_form')) {
            header('Location: /auth/forgot?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $email = $_POST['email'];

            if (UserDataHandler::doesEmailExist($email)) {
                $token = UserDataHandler::getTokenEmail($email);
                if (MailService::isEnabled() == true) {
                    Forgot::sendMail($token);
                    UserActivity::addActivity(UserDataHandler::getSpecificUserData($token, 'uuid', false), 'Email password reset request sent!', CloudFlare::getUserIP(), 'user:forgot-password');
                    header('Location: /auth/login?s=password_forgot');
                    exit;
                }
                header('Location: /auth/forgot?e=mailserver_misconfiguration');
                exit;

            }
            header('Location: /auth/forgot?e=user_not_found');
            exit;

        }
        header('Location: /auth/forgot?e=captcha');
        exit;
    }
});

/*
 *
 * Verify Email
 *
 * This route will handle the email verification.
 *
 * @return void
 *
 */
$router->add('/auth/verify-email', function (): void {

    global $renderer;

    if (isset($_GET['token']) && !$_GET['token'] == '') {
        if (MailVerification::isValid($_GET['token'])) {
            $token = UserDataHandler::getTokenUUID(MailVerification::getUserUUID($_GET['token']));
            $user = new UserHelper($token);
            $user->verifyUser();
            setcookie('token', $token, time() + 3600 * 24 * 365 * 5, '/');
            MailVerification::remove($_GET['token']);
            UserActivity::addActivity(UserDataHandler::getSpecificUserData($token, 'uuid', false), 'Email verification success!', CloudFlare::getUserIP(), 'user:email-verified');
            exit(header('Location: /auth/login?s=mail_verify'));
        }
        exit(header('Location: /auth/login?e=code_invalid'));
    }
    exit(header('Location: /auth/login?e=code_not_exist'));
});

/*
 *
 * Login
 *
 * This route will handle the login.
 *
 * @return void
 */
$router->add('/auth/login', function (): void {
    /*
     * The requirement for each template
     */

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $template_name = 'auth/login.twig';
    global $renderer;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('login_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());

        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('login_form')) {
            header('Location: /auth/login?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user_check = UserDataHandler::login($email, $password, CloudFlare::getUserIP());
            if ($user_check == 'ERROR_USER_NOT_FOUND') {
                header('Location: /auth/login?e=user_not_found');
                exit;
            }

            if ($user_check == 'ERROR_USER_NOT_VERIFIED') {
                header('Location: /auth/login?e=user_not_verified');
                exit;
            }

            if ($user_check == 'ERROR_USER_BANNED') {
                header('Location: /auth/login?e=user_banned');
                exit;
            }

            if ($user_check == 'ERROR_USER_DELETED') {
                header('Location: /auth/login?e=user_deleted');
                exit;
            }

            if ($user_check == 'ERROR_PASSWORD_INCORRECT') {
                header('Location: /auth/login?e=password_not_valid');
                exit;
            }

            if (strpos($user_check, 'mythicalframework_') === 0) {
                Login::sendMail($user_check);
                setcookie('token', $user_check, time() + 3600 * 24 * 365 * 5, '/');
                header('Location: /');
                UserActivity::addActivity(UserDataHandler::getSpecificUserData($user_check, 'uuid', false), 'User logged in!', CloudFlare::getUserIP(), 'user:login');
                $userTwoFactor = new TwoFactor($user_check);
                $userTwoFactor->block();
                exit;
            }
            header('Location: /auth/login?e=unknown');
            exit;
        }
        header('Location: /auth/login?e=captcha');
        exit;
    }
});

$router->add('/auth/2fa/disable', function () : void {
    global $renderer;

    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token'], true);

    $user2fa = new TwoFactor($_COOKIE['token']);
    if ($user2fa->isSetup() == false) {
        header('Location: /dashboard?e=2fa_not_setup');
        exit;
    }

    $user2fa->unblock();
    $user2fa->disable();
    exit(header('Location: /account/security?s=2fa_setup_disabled'));
});

/**
 * 2FA Login.
 *
 * This route will handle the 2FA login.
 */
$router->add('/auth/2fa/login', function (): void {
    global $renderer;
    $template_name = 'auth/2fa/login.twig';

    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token'], true);

    $user2fa = new TwoFactor($_COOKIE['token']);
    if ($user2fa->isSetup() == false) {
        header('Location: /dashboard?e=2fa_not_setup');
        exit;
    }

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $google2fa = new Google2FA();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', value: $csrf->input('2fa_setup_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name));
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('2fa_setup_form')) {
            header('Location: /auth/2fa/setup?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $final_pin = $_POST['final_pin'];
            if ($google2fa->verifyKey($user2fa->getKey(), $final_pin)) {
                $user2fa->unblock();
                UserActivity::addActivity(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false), '2FA login success!', CloudFlare::getUserIP(), 'user:2fa-login');
                header('Location: /account/security?s=2fa_setup_success');
                exit;
            }
            header('Location: /auth/2fa/login?e=2fa_failed');
            exit;
        }
        header('Location: /auth/2fa/login?e=captcha');
        exit;
    }
});

/*
 *
 * 2FA Setup
 * This route will handle the 2FA setup.
 * The user will be able to setup 2FA for his account.
 *
 * @return void
 */
$router->add('/auth/2fa/setup', function (): void {
    global $renderer;
    $template_name = 'auth/2fa/setup.twig';

    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    $user2fa = new TwoFactor($_COOKIE['token']);
    if ($user2fa->isSetup()) {
        header('Location: /dashboard?e=2fa_already_setup');
        exit;
    }

    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();
    $google2fa = new Google2FA();
    $secretKey = $google2fa->generateSecretKey();
    $qr = TwoFactorAuthentication::buildQRCode($secretKey, 'NaysKutzu');

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', value: $csrf->input('2fa_setup_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
        $renderer->addGlobal('qr_code', $qr);
        $renderer->addGlobal('secret_key', $secretKey);

        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('2fa_setup_form')) {
            header('Location: /auth/2fa/setup?e=csrf');
            exit;
        }
        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $final_pin = $_POST['final_pin'];
            $secretKey = $_POST['secret_key'];

            if ($google2fa->verifyKey($secretKey, $final_pin)) {
                $user2fa->enable($secretKey);
                UserActivity::addActivity(UserDataHandler::getSpecificUserData($_COOKIE['token'], 'uuid', false), '2FA setup success!', CloudFlare::getUserIP(), 'user:2fa-setup');
                header('Location: /dashboard?s=2fa_setup_success');
                exit;
            }
            header('Location: /auth/2fa/setup?e=2fa_failed');
            exit;
        }
        header('Location: /auth/2fa/setup?e=captcha');
        exit;
    }
});
/*
 *  Register
 *
 * This route will handle the registration of a new user.
 *
 * @return void
 *
 */
$router->add('/auth/register', function (): void {
    /*
     * The requirement for each template
     */
    $template_name = 'auth/register.twig';
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    global $renderer;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('register_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());

        Engine::registerAlerts($renderer, $template_name);

        exit($renderer->render($template_name));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('register_form')) {
            header('Location: /auth/register?e=csrf');
            exit;
        }

        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST['cf-turnstile-response'], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $first_name = $_POST['firstname'];
            $last_name = $_POST['lastname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user_check = UserDataHandler::create($username, $password, $email, $first_name, $last_name, CloudFlare::getUserIP());

            if ($user_check == 'ERROR_USERNAME_EXISTS') {
                header('Location: /auth/register?e=username_exists');
                exit;
            }

            if ($user_check == 'ERROR_EMAIL_EXISTS') {
                header('Location: /auth/register?e=email_exists');
                exit;
            }

            if ($user_check == 'ERROR_DATABASE_INSERT_FAILED') {
                header('Location: /auth/register?e=unknown');
                exit;
            }
            if (strpos($user_check, 'mythicalframework_') === 0) {
                if (MailService::isEnabled()) {
                    Verification::sendMail($user_check);
                    header('Location: /auth/login?s=mail_verify');
                    exit;
                }
                $user = new UserHelper($user_check);
                $user->verifyUser();
                setcookie('token', $user_check, time() + 3600 * 24 * 365 * 5, '/');
                header('Location: /auth/login?s=register');
                exit;
            }
            header('Location: /auth/register?e=unknown');
            exit;
        }
        header('Location: /auth/register?e=captcha');
        exit;
    }
});

/*
 *
 * Logout
 *
 * This route will handle the logout.
 *
 * @return void
 */
$router->add('/auth/logout', function (): void {
    $user = new UserHelper($_COOKIE['token']);
    $user->killSession();
    header('Location: /');
    exit;
});
