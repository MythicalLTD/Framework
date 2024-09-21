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

namespace MythicalSystemsFramework\Mail\Templates;

use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\Mail\MailBox;
use MythicalSystemsFramework\User\UserDataHandler;
use MythicalSystemsFramework\Mail\MailVerification;

class Verification extends MailService
{
    public static function sendMail(string $token): bool
    {

        if (self::doesTemplateExist('verify')) {
            $template = self::getTemplate('verify');
            $template = self::processTemplateSystemLevel($template);
            $template = self::processTemplateUserLevel($template, $token);
            $verify_code = self::generatePin(24);
            $template = str_replace('{token}', $verify_code, $template);
            MailVerification::add(UserDataHandler::getSpecificUserData($token, 'uuid', false), $verify_code);
            MailBox::saveEmail('Verify your account', $template, Settings::getSetting('smtp', 'fromMail'), $token);
            if (self::send(UserDataHandler::getSpecificUserData($token, 'email', false), 'Verify your account', $template)) {
                return true;
            }

            return false;

        }

        return false;

    }

    private static function generatePin(int $length = 8): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
