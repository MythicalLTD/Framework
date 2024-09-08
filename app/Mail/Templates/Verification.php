<?php

namespace MythicalSystemsFramework\Mail\Templates;
use MythicalSystemsFramework\Mail\EmailTemplate;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\User\UserDataHandler;

class Verification extends MailService {
    public static function sendMail(string $uuid) : void {
        if (self::doesTemplateExist("verify")) {
            $template = self::getTemplate("verify");
            $template = self::processTemplateSystemLevel($template);
            $template = self::processTemplateUserLevel($template, UserDataHandler::getTokenByUserID($uuid));
            die($template);
        } else {
            return;
        }
    }
}