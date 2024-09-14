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

class CloudFlare extends \MythicalSystems\CloudFlare\CloudFlare
{
    /**
     * Get the ip of a user.
     *
     * @return string|null The ipv4 or ipv6 or null incase if the ip is not valid or was tampered with!
     */
    public static function getUserIP(): ?string
    {
        global $event;
        $ip = \MythicalSystems\CloudFlare\CloudFlare::getRealUserIP();
        // Check if the ip is valid
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $event->emit('cloudflare.onGetUserIP', [$ip]);

            return $ip;
        }

        return null;

    }

    /**
     * DEPRECATED: Use getUserIP() instead.
     */
    public static function getRealUserIP(): string
    {
        return self::getUserIP();
    }
}
