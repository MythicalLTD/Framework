<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

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
            }

            return false;

        } catch (\Exception $e) {
            return false;
        }
    }
}
