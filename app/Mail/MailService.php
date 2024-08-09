<?php

namespace MythicalSystemsFramework\Mail;

use MythicalSystemsFramework\Managers\Settings as setting;

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
}
