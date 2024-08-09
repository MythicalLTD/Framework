<?php

namespace MythicalSystemsFramework\CloudFlare;

use MythicalSystemsFramework\App;
use MythicalSystemsFramework\Managers\Settings as setting;

class TurnStile extends \MythicalSystems\CloudFlare\Turnstile
{
    /**
     * Is cloudflare turnstile enabled?
     */
    public static function isEnabled(): bool
    {
        return App::convertStringToBool(setting::getSetting('cloudflare_turnstile', 'enabled'));
    }
}
