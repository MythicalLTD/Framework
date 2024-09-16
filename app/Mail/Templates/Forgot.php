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

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\Mail\MailBox;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\UserDataHandler;

class Forgot extends MailService
{
    public static function sendMail(string $token): bool
    {

        if (self::doesTemplateExist('login')) {
            $template = self::getTemplate('login');
            $template = self::processTemplateSystemLevel($template);
            $template = self::processTemplateUserLevel($template, $token);
            MailBox::saveEmail('New login in your account', $template, Settings::getSetting('smtp', 'fromMail'), $token);
            if (self::send(UserDataHandler::getSpecificUserData($token, 'email', false), 'New login in your account', $template)) {
                return true;
            }
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, '(App/Mail/Templates/Login.php) Failed to send email. Email library failed.');

            return false;

        }
        Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, '(App/Mail/Templates/Login.php) Failed to send email. Template does not exist.');

        return false;

    }
}
