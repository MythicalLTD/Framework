<?php

namespace MythicalSystemsFramework\Mail;

use MythicalSystemsFramework\Managers\Settings as setting;
use MythicalSystemsFramework\User\UserHelper;

class MailService
{
    /**
     * Is the mail server enabled?
     */
    public static function isEnabled(): bool
    {
        if (setting::getSetting('mail', 'enabled') == 'true') {
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
        $user = new UserHelper($token);
        $template = str_replace("{username}", $user->getInfo("username",false), $template);
        $template = str_replace("{email}", $user->getInfo("email",false), $template);
        $template = str_replace("{first_name}", $user->getInfo("first_name",true), $template);
        $template = str_replace("{last_name}", $user->getInfo("last_name",true), $template);        
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
