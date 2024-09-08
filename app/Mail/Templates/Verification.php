<?php

namespace MythicalSystemsFramework\Mail\Templates;
use MythicalSystemsFramework\Mail\EmailTemplate;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\User\Mail\MailBox;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\UserDataHandler;

class Verification extends MailService {
    public static function sendMail(string $token) : bool {
        
        if (self::doesTemplateExist("verify")) {
            $template = self::getTemplate("verify");
            $template = self::processTemplateSystemLevel($template);
            $template = self::processTemplateUserLevel($template, $token);
            $template = str_replace("{token}", self::generatePin(24), $template);
            MailBox::saveEmail("Verify your account", $template, Settings::getSetting('smtp', 'fromMail'), $token);
            if (self::send(UserDataHandler::getSpecificUserData($token, "email", false), "Verify your account", $template)) {
                return true;
            } else {
                return false; 
            }
        } else {
            return false;
        }
    }

    private static function generatePin(int $length = 8) : string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}