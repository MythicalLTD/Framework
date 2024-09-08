<?php

use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\CloudFlare\TurnStile;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Mail\Templates\Verification;
use MythicalSystemsFramework\Managers\LanguageManager;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\User\UserHelper;

global $router;

$router->add('/auth/register', function () {
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
            exit();
        }

        if (!TurnStile::isEnabled()) {
            $captcha_success = 1;
        } else {
            $captcha_success = TurnStile::validate($_POST["cf-turnstile-response"], CloudFlare::getUserIP(), Settings::getSetting('cloudflare_turnstile', 'sitesecret'));
        }

        if ($captcha_success) {
            $first_and_last_name = $_POST['first_and_last_name'];
            $first_name = explode(' ', $first_and_last_name)[0];
            $last_name = explode(' ', $first_and_last_name)[1];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user_check = UserDataHandler::create($username, $password, $email, $first_name, $last_name, CloudFlare::getUserIP());

            if ($user_check == "ERROR_USERNAME_EXISTS") {
                header('Location: /auth/register?e=username_exists');
                exit();
            }

            if ($user_check == "ERROR_EMAIL_EXISTS") {
                header('Location: /auth/register?e=email_exists');
                exit();
            }

            if ($user_check == "ERROR_DATABASE_INSERT_FAILED") {
                header('Location: /auth/register?e=unknown');
                exit();
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
                exit();
            }
        } else {
            header('Location: /auth/register?e=captcha');
            exit();
        }
    }
});
