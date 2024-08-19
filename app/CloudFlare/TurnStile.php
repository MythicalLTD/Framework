<?php

namespace MythicalSystemsFramework\CloudFlare;

use MythicalSystemsFramework\Managers\Settings as setting;

class TurnStile extends \MythicalSystems\CloudFlare\Turnstile
{
    /**
     * Is cloudflare turnstile enabled?
     */
    public static function isEnabled(): bool
    {
        try {
            if (setting::getSetting('cloudflare_turnstile', 'enabled') == 'true') {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
