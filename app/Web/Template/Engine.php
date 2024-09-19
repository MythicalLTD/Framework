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

namespace MythicalSystemsFramework\Web\Template;

use MythicalSystemsFramework\CloudFlare\TurnStile;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use MythicalSystemsFramework\Language\Manager;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Web\Installer\Installer;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Engine
{
    /**
     * Add the requirements for the template engine.
     */
    public static function getRenderer(?string $cache_dir = null, ?string $theme_dir = null, bool $debug = false): Environment
    {
        if ($cache_dir == null) {
            define('DIR_TEMPLATE', __DIR__ . '/../../../storage/themes/' . Settings::getSetting('app', 'theme'));
        } else {
            define('DIR_TEMPLATE', $theme_dir);
        }

        if ($theme_dir == null) {
            define('DIR_CACHE', __DIR__ . '/../../../storage/caches');
        } else {
            define('DIR_CACHE', $cache_dir);
        }

        // if ($debug) {
        //    define('DEBUG', true);
        // } else {
        //    define('DEBUG', false);
        // }
        define('DEBUG', true);

        /*
         * Load the template engine
         */

        if (!is_dir(DIR_TEMPLATE)) {
            Installer::showError('The theme directory does not exist!');
        }

        if (!is_dir(DIR_CACHE)) {
            mkdir(DIR_CACHE, 0777, true);
        }

        $loader = new FilesystemLoader(DIR_TEMPLATE);
        $renderer = new Environment($loader, [
            // 'cache' => DIR_CACHE,
            'auto_reload' => true,
            'debug' => DEBUG,
            'charset' => 'utf-8',
            'no_cache' => true,
            'cache' => false,
        ]);

        self::registerSettings($renderer);
        self::registerConfig($renderer);
        self::registerLanguage($renderer);
        self::registerGlobals($renderer);

        return $renderer;
    }

    /**
     * Register the config function.
     */
    public static function registerConfig(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('cfg', function ($section, $key): string {
            return cfg::get($section, $key);
        }));
    }

    /**
     * Register the language function.
     */
    public static function registerLanguage(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('lang', function ($key): ?string {
            $lang = new Manager();

            return $lang->get($key);
        }));
    }

    /**
     * Register the settings function.
     */
    public static function registerSettings(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('setting', function ($section, $key): string {
            return Settings::getSetting($section, $key);
        }));

        $renderer->addFunction(new TwigFunction('settings', function ($section, $key): string {
            return Settings::getSetting($section, $key);
        }));
    }

    /**
     * Register global values into twig.
     */
    public static function registerGlobals(Environment $renderer): void
    {
        $renderer->addGlobal('php_version', phpversion());
        $renderer->addGlobal('page_name', 'Home');
        $renderer->addGlobal('isTurnStileEnabled', TurnStile::isEnabled());
    }

    public static function registerAlerts(Environment $renderer, string $template_name): void
    {
        $warnings = [];
        $errors = [];
        $success = [];

        if (isset($_GET['e']) && !$_GET['e'] == '') {
            $e = $_GET['e'];
            $error_title = self::getError('UnknownError.Title');
            $error_message = self::getError('UnknownError.Message');
            switch ($e) {
                case 'csrf':
                    $error_title = self::getError('CSRF.Title');
                    $error_message = self::getError('CSRF.Message');
                    break;
                case 'captcha':
                    $error_title = self::getError('Captcha.Title');
                    $error_message = self::getError('Captcha.Message');
                    break;
                case 'user_not_found':
                    $error_title = self::getError('UserNotFound.Title');
                    $error_message = self::getError('UserNotFound.Message');
                    break;
                case 'user_banned':
                    $error_title = self::getError('UserBanned.Title');
                    $error_message = self::getError('UserBanned.Message');
                    break;
                case 'user_not_verified':
                    $error_title = self::getError('UserNotVerified.Title');
                    $error_message = self::getError('UserNotVerified.Message');
                    break;
                case 'code_invalid':
                    $error_title = self::getError('CodeInvalid.Title');
                    $error_message = self::getError('CodeInvalid.Message');
                    break;
                case 'code_expired':
                    $error_title = self::getError('CodeExpired.Title');
                    $error_message = self::getError('CodeExpired.Message');
                    break;
                case 'code_not_exist':
                    $error_title = self::getError('CodeDoesNotExist.Title');
                    $error_message = self::getError('CodeDoesNotExist.Message');
                    break;
                case 'user_deleted':
                    $error_title = self::getError('UserDeleted.Title');
                    $error_message = self::getError('UserDeleted.Message');
                    break;
                case 'missing_fields':
                    $error_title = self::getError('PleaseFillAllFields.Title');
                    $error_message = self::getError('PleaseFillAllFields.Message');
                    break;
                case 'password_week':
                    $error_title = self::getError('PasswordToWeek.Title');
                    $error_message = self::getError('PasswordToWeek.Message');
                    break;
                case 'password_not_valid':
                    $error_title = self::getError('InfoNotValid.Password.Title');
                    $error_message = self::getError('InfoNotValid.Password.Message');
                    break;
                case 'email_not_valid':
                    $error_title = self::getError('InfoNotValid.Email.Title');
                    $error_message = self::getError('InfoNotValid.Email.Message');
                    break;
                case 'username_not_valid':
                    $error_title = self::getError('InfoNotValid.Username.Title');
                    $error_message = self::getError('InfoNotValid.Username.Message');
                    break;
                case 'first_name_not_valid':
                    $error_title = self::getError('InfoNotValid.FirstName.Title');
                    $error_message = self::getError('InfoNotValid.FirstName.Message');
                    break;
                case 'last_name_not_valid':
                    $error_title = self::getError('InfoNotValid.LastName.Title');
                    $error_message = self::getError('InfoNotValid.LastName.Message');
                    break;
                case 'password_same_as_username':
                    $error_title = self::getError('PasswordCantBeSameAs.Username.Title');
                    $error_message = self::getError('PasswordCantBeSameAs.Username.Message');
                    break;
                case 'password_same_as_email':
                    $error_title = self::getError('PasswordCantBeSameAs.Email.Title');
                    $error_message = self::getError('PasswordCantBeSameAs.Email.Message');
                    break;
                case 'password_same_as_first_name':
                    $error_title = self::getError('PasswordCantBeSameAs.FirstName.Title');
                    $error_message = self::getError('PasswordCantBeSameAs.FirstName.Message');
                    break;
                case 'password_same_as_last_name':
                    $error_title = self::getError('PasswordCantBeSameAs.LastName.Title');
                    $error_message = self::getError('PasswordCantBeSameAs.LastName.Message');
                    break;
                case 'username_exists':
                    $error_title = self::getError('UsernameExists.Title');
                    $error_message = self::getError('UsernameExists.Message');
                    break;
                case 'email_exists':
                    $error_title = self::getError('EmailExists.Title');
                    $error_message = self::getError('EmailExists.Message');
                    break;
                case '2fa_already_setup':
                    $error_title = self::getError('TwoFactorSetup.AlreadySetup.Title');
                    $error_message = self::getError('TwoFactorSetup.AlreadySetup.Message');
                    break;
                case '2fa_not_setup':
                    $error_title = self::getError('TwoFactorSetup.NotSetup.Title');
                    $error_message = self::getError('TwoFactorSetup.NotSetup.Message');
                    break;
                case '2fa_failed':
                    $error_title = self::getError('TwoFactorSetup.InvalidCode.Title');
                    $error_message = self::getError('TwoFactorSetup.InvalidCode.Message');
                    break;
                case 'unknown':
                    $error_title = self::getError('UnknownError.Title');
                    $error_message = self::getError('UnknownError.Message');
                    break;
                case 'mailserver_misconfiguration':
                    $error_title = self::getError('EmailServerNotConfigured.Title');
                    $error_message = self::getError('EmailServerNotConfigured.Message');
                    break;
                case 'already_liked':
                    $error_title = self::getError('Social.AlreadyLiked.Title');
                    $error_message = self::getError('Social.AlreadyLiked.Message');
                    break;
                case 'like_yourself':
                    $error_title = self::getError('Social.CannotLikeYourSelf.Title');
                    $error_message = self::getError('Social.CannotLikeYourSelf.Message');
                    break;
                case 'user_not_own_object':
                    $error_title = self::getError('UserDoesNotOwnTarget.Title');
                    $error_message = self::getError('UserDoesNotOwnTarget.Message');
                    break;
                default:
                    $error_title = self::getError('UnknownError.Title');
                    $error_message = self::getError('UnknownError.Message');
                    break;
            }

            $errors['alert_error_title'] = $error_title;
            $errors['alert_error_message'] = $error_message;

            exit($renderer->render($template_name, $errors));
        }

        if (isset($_GET['s']) && !$_GET['s'] == '') {
            $s = $_GET['s'];
            $success_title = self::getSuccess('ActionSuccessful.Title');
            $success_message = self::getSuccess('ActionSuccessful.Message');
            switch ($s) {
                case '2fa_setup_success':
                    $success_title = self::getSuccess('Auth.TwoFactorSetup.Setup.Title');
                    $success_message = self::getSuccess('Auth.TwoFactorSetup.Setup.Message');
                    break;
                case '2fa_setup_disabled':
                    $success_title = self::getSuccess('Auth.TwoFactorSetup.Disable.Title');
                    $success_message = self::getSuccess('Auth.TwoFactorSetup.Disable.Message');
                    break;
                case '2fa_setup_code_correct':
                    $success_title = self::getSuccess('Auth.TwoFactorSetup.Verify.Title');
                    $success_message = self::getSuccess('Auth.TwoFactorSetup.Verify.Message');
                    break;
                case 'login':
                    $success_title = self::getSuccess('Auth.Login.Title');
                    $success_message = self::getSuccess('Auth.Login.Message');
                    break;
                case 'register':
                    $success_title = self::getSuccess('Auth.Register.Title');
                    $success_message = self::getSuccess('Auth.Register.Message');
                    break;
                case 'logout':
                    $success_title = self::getSuccess('Auth.Logout.Title');
                    $success_message = self::getSuccess('Auth.Logout.Message');
                    break;
                case 'password_reset':
                    $success_title = self::getSuccess('Auth.ResetPassword.Title');
                    $success_message = self::getSuccess('Auth.ResetPassword.Message');
                    break;
                case 'password_forgot':
                    $success_title = self::getSuccess('Auth.ForgotPassword.Title');
                    $success_message = self::getSuccess('Auth.ForgotPassword.Message');
                    break;
                case 'mail_verify':
                    $success_title = self::getSuccess('Auth.VerifyEmail.Title');
                    $success_message = self::getSuccess('Auth.VerifyEmail.Message');
                    break;
                case 'liked':
                    $success_title = self::getSuccess('Social.Liked.Title');
                    $success_message = self::getSuccess('Social.Liked.Message');
                    break;
                case 'disliked':
                    $success_title = self::getSuccess('Social.UnLiked.Title');
                    $success_message = self::getSuccess('Social.UnLiked.Message');
                    break;
                default:
                    break;
            }
            $success['alert_success_title'] = $success_title;
            $success['alert_success_message'] = $success_message;
            exit($renderer->render($template_name, $success));
        }

        if (isset($_GET['w']) && !$_GET['w'] == '') {
            $w = $_GET['w'];
            switch ($w) {
            }
        }
    }

    private static function getError(string $key): ?string
    {
        $lang = new Manager();

        return $lang->get('Alerts.Error.' . $key);
    }

    private static function getSuccess(string $key): ?string
    {
        $lang = new Manager();

        return $lang->get('Alerts.Success.' . $key);
    }

    private static function getWarnings(string $key): ?string
    {
        $lang = new Manager();

        return $lang->get('Alerts.Warnings.' . $key);
    }
}
