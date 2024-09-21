<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

namespace MythicalSystemsFramework\Google;

use MythicalSystemsFramework\Managers\Settings as settings;

class TwoFactorAuthentication
{
    /**
     * Build a QR code for the user to scan.
     *
     * @param string $key The secret key
     * @param string $username The username
     *
     * @return string The QR code URL
     */
    public static function buildQRCode(string $key, string $username): string
    {
        return sprintf('https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=%s&ecc=M', rawurlencode(sprintf('otpauth://totp/%s?secret=%s&issuer=%s', $username, $key, urlencode(settings::getSetting('app', 'name')))));
    }
}
