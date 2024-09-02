<?php

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
        } else {
            return null;
        }
    }

    /**
     * DEPRECATED: Use getUserIP() instead.
     */
    public static function getRealUserIP(): string
    {
        return self::getUserIP();
    }
}
