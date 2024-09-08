<?php

namespace MythicalSystemsFramework\Mail;

use MythicalSystemsFramework\Managers\Settings as setting;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\User\UserHelper;

class MailService
{

    public static function send(string $to, string $subject, string $message) : bool
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
                $mail->send();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
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
        } else {
            return false;
        }
    }
    /**
     * Process the template at a user level
     * 
     * @param string $template
     * @return string
     */
    public static function processTemplateUserLevel(string $template,string $token) : string {
        $username = UserDataHandler::getSpecificUserData($token, "username",false);
        $email = UserDataHandler::getSpecificUserData($token, "email",false);
        $first_name = UserDataHandler::getSpecificUserData($token, "first_name",true);
        $last_name = UserDataHandler::getSpecificUserData($token, "last_name",true);

        $template = str_replace("{username}", $username, $template);
        $template = str_replace("{email}", $email, $template);
        $template = str_replace("{first_name}", $first_name, $template);
        $template = str_replace("{last_name}", $last_name, $template);  

        return $template;
    }

    /**
     * Process the template at a system level (app_name, app_logo, app_url, support_mail)
     * @param string $template
     * @return string
     */
    public static function processTemplateSystemLevel(string $template) : string {
        $template = str_replace("{app_name}", setting::getSetting('app', 'name'), $template);
        $template = str_replace("{app_logo}", setting::getSetting('app', 'logo'), $template);
        $template = str_replace("{app_url}", setting::getSetting('app', 'url'), $template);
        $template = str_replace("{support_mail}", setting::getSetting('smtp', 'fromMail'), $template);
        return $template;
    }

    /**
     * Get the template
     * 
     * @param string $template The template you want to get
     * 
     * @return string
     */
    public static function getTemplate(string $template): string
    {
        $template_dir = __DIR__ . "/../../storage/mails/";
        $template_file = $template_dir . $template . ".html";
        if (self::doesTemplateExist($template)) {
            return file_get_contents($template_file);
        } else {
            return "";
        }
    }

    /**
     * Does the template exist?
     * 
     * @param string $template The template you want to check if it exists
     * 
     * @return bool
     */
    public static function doesTemplateExist(string $template): bool
    {
        $template_dir = __DIR__ . "/../../storage/mails/";
        $template_file = $template_dir . $template . ".html";
        if (file_exists($template_file)) {
            return true;
        } else {
            return false;
        }
    }
}
