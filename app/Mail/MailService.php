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

namespace MythicalSystemsFramework\Mail;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\Managers\Settings as setting;

class MailService
{
    public static function send(string $to, string $subject, string $message): bool
    {
        if (self::isEnabled()) {
            $from = setting::getSetting('smtp', 'fromMail');
            $from_name = setting::getSetting('app', 'name');
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = setting::getSetting('smtp', 'host');
                $mail->SMTPAuth = true;
                $mail->Username = setting::getSetting('smtp', 'username');
                $mail->Password = setting::getSetting('smtp', 'password');
                $mail->SMTPSecure = setting::getSetting('smtp', 'secure');
                $mail->Port = setting::getSetting('smtp', 'port');
                $mail->setFrom($from, $from_name);
                $mail->addReplyTo($from, $from_name);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->addAddress($to);
                $mail->send();

                return true;
            } catch (\Exception $e) {
                Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailService.php) Failed to send email. ' . $e->getMessage());

                return false;
            }
        } else {
            Logger::log(LoggerLevels::CRITICAL, LoggerLevels::OTHER, '(App/Mail/MailService.php) Failed to send email. SMTP is not enabled.');

            return false;
        }

    }

    /**
     * Is the mail server enabled?
     */
    public static function isEnabled(): bool
    {
        if (setting::getSetting('smtp', 'enabled') == 'true') {
            return true;
        }

        return false;

    }

    /**
     * Process the template at a user level.
     */
    public static function processTemplateUserLevel(string $template, string $token): string
    {
        $username = UserDataHandler::getSpecificUserData($token, 'username', false);
        $email = UserDataHandler::getSpecificUserData($token, 'email', false);
        $first_name = UserDataHandler::getSpecificUserData($token, 'first_name', true);
        $last_name = UserDataHandler::getSpecificUserData($token, 'last_name', true);

        $template = str_replace('{username}', $username, $template);
        $template = str_replace('{email}', $email, $template);
        $template = str_replace('{first_name}', $first_name, $template);

        return str_replace('{last_name}', $last_name, $template);
    }

    /**
     * Process the template at a system level (app_name, app_logo, app_url, support_mail).
     */
    public static function processTemplateSystemLevel(string $template): string
    {
        $template = str_replace('{app_name}', setting::getSetting('app', 'name'), $template);
        $template = str_replace('{app_logo}', setting::getSetting('app', 'logo'), $template);
        $template = str_replace('{app_url}', setting::getSetting('app', 'url'), $template);

        return str_replace('{support_mail}', setting::getSetting('smtp', 'fromMail'), $template);
    }

    /**
     * Get the template.
     *
     * @param string $template The template you want to get
     */
    public static function getTemplate(string $template): string
    {
        $template_dir = __DIR__ . '/../../storage/mails/';
        $template_file = $template_dir . $template . '.html';
        if (self::doesTemplateExist($template)) {
            return file_get_contents($template_file);
        }

        return '';

    }

    /**
     * Does the template exist?
     *
     * @param string $template The template you want to check if it exists
     */
    public static function doesTemplateExist(string $template): bool
    {
        $template_dir = __DIR__ . '/../../storage/mails/';
        $template_file = $template_dir . $template . '.html';
        if (file_exists($template_file)) {
            return true;
        }

        return false;

    }
}
