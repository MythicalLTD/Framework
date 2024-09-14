<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

use PragmaRX\Google2FA\Google2FA;
use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\Mail\Templates\Login;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Mail\MailVerification;
use MythicalSystemsFramework\Managers\LanguageManager;
use MythicalSystemsFramework\Mail\Templates\Verification;
use MythicalSystemsFramework\Google\TwoFactorAuthentication;

global $router;

$router->add('/auth/verify-email', function (): void {
    $lang = LanguageManager::getLang();
    global $renderer;

    if (isset($_GET['token']) && !$_GET['token'] == '') {
        if (MailVerification::isValid($_GET['token'])) {
            $token = UserDataHandler::getTokenByUserID(MailVerification::getUserUUID($_GET['token']));
            $user = new UserHelper($token);
            $user->verifyUser();
            setcookie('token', $token, time() + 3600 * 24 * 365 * 5, '/');
            MailVerification::remove($_GET['token']);
            exit($renderer->render('index.twig', ['alert_success_title' => $lang['alert_title_success'], 'alert_success_message' => $lang['alert_email_verified']]));
        }
        exit($renderer->render('index.twig', ['alert_error_title' => $lang['alert_title_error'], 'alert_error_message' => $lang['alert_email_verification_code_does_not_exist']]));

    }
    exit($renderer->render('index.twig', ['alert_error_title' => $lang['alert_title_error'], 'alert_error_message' => $lang['alert_email_verification_code_does_not_exist']]));

});

$router->add('/auth/login', function (): void {
    /*
     * The requirement for each template
     */
    $lang = LanguageManager::getLang();
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    global $renderer;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('login_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());

        if (isset($_GET['e']) && !$_GET['e'] == '') {
            $e = $_GET['e'];
            if ($e == 'csrf') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_alert_error'], 'alert_error_message' => $lang['alert_csrf_failed']]));
            }

            if ($e == 'captcha') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_alert_error'], 'alert_error_message' => $lang['alert_captcha_failed']]));
            }

            if ($e == 'user_not_found') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_alert_error'], 'alert_error_message' => $lang['pages_login_user_not_found']]));
            }

            if ($e == 'unknown') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_alert_error'], 'alert_error_message' => $lang['alert_unknown_error']]));
            }

            if ($e == 'user_not_verified') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_user_not_verified'], 'alert_error_message' => $lang['pages_login_user_not_verified_description']]));
            }

            if ($e == 'user_banned') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_user_banned'], 'alert_error_message' => $lang['pages_login_user_banned_description']]));
            }

            if ($e == 'user_deleted') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_user_deleted'], 'alert_error_message' => $lang['pages_login_user_deleted_description']]));
            }

            if ($e == 'user_password_incorrect') {
                exit($renderer->render('auth/login.twig', ['alert_error_title' => $lang['pages_login_user_password_incorrect'], 'alert_error_message' => $lang['pages_login_user_password_incorrect_description']]));
            }
        }
        exit($renderer->render('auth/login.twig'));
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
                header('Location: /auth/login?e=user_password_incorrect');
                exit;
            }

            if (strpos($user_check, 'mythicalframework_') === 0) {
                Login::sendMail($user_check);
                setcookie('token', $user_check, time() + 3600 * 24 * 365 * 5, '/');
                header('Location: /');
            } else {
                header('Location: /auth/login?e=unknown');
                exit;
            }
        } else {
            header('Location: /auth/login?e=captcha');
            exit;
        }
    }
});

$router->add('/auth/2fa/setup', function (): void {
    global $renderer;

    $user = new UserHelper($_COOKIE['token']);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    -
    $lang = LanguageManager::getLang();
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    Debugger::ShowAllErrors();
    $google2fa = new Google2FA();
    $secretKey = $google2fa->generateSecretKey();

    $qr = TwoFactorAuthentication::buildQRCode($secretKey, 'NaysKutzu');

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('2fa_setup_form'));
        exit($renderer->render('auth/2fa_setup.twig', ['qrCode' => $qr, 'secret' => $secretKey]));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$csrf->validate('2fa_setup_form')) {
            header('Location: /auth/2fa/setup?e=csrf');
            exit;
        }
    }
});

$router->add('/auth/register', function (): void {
    /*
     * The requirement for each template
     */
    $lang = LanguageManager::getLang();
    session_start();
    $csrf = new MythicalSystems\Utils\CSRFHandler();

    global $renderer;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('csrf_input', $csrf->input('register_form'));
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());

        if (isset($_GET['e']) && !$_GET['e'] == '') {
            $e = $_GET['e'];
            if ($e == 'csrf') {
                exit($renderer->render('auth/register.twig', ['alert_error_title' => $lang['pages_register_alert_error'], 'alert_error_message' => $lang['alert_csrf_failed']]));
            }

            if ($e == 'captcha') {
                exit($renderer->render('auth/register.twig', ['alert_error_title' => $lang['pages_register_alert_error'], 'alert_error_message' => $lang['alert_captcha_failed']]));
            }

            if ($e == 'username_exists') {
                exit($renderer->render('auth/register.twig', ['alert_error_title' => $lang['pages_register_alert_error'], 'alert_error_message' => $lang['alert_username_exists']]));
            }

            if ($e == 'email_exists') {
                exit($renderer->render('auth/register.twig', ['alert_error_title' => $lang['pages_register_alert_error'], 'alert_error_message' => $lang['alert_email_exists']]));
            }

            if ($e == 'unknown') {
                exit($renderer->render('auth/register.twig', ['alert_error_title' => $lang['pages_register_alert_error'], 'alert_error_message' => $lang['alert_unknown_error']]));
            }
        }
        if (isset($_GET['s']) && !$_GET['s'] == '') {
        }

        exit($renderer->render('auth/register.twig'));
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
            $first_and_last_name = $_POST['first_and_last_name'];
            $first_name = explode(' ', $first_and_last_name)[0];
            $last_name = explode(' ', $first_and_last_name)[1];
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
                } else {
                    $user = new UserHelper($user_check);
                    $user->verifyUser();
                    setcookie('token', $user_check, time() + 3600 * 24 * 365 * 5, '/');
                }
                header('Location: /');
            } else {
                header('Location: /auth/register?e=unknown');
                exit;
            }
        } else {
            header('Location: /auth/register?e=captcha');
            exit;
        }
    }
});
