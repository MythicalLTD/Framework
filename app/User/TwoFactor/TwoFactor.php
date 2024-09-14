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

namespace MythicalSystemsFramework\User\TwoFactor;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\UserDataHandler;

class TwoFactor
{
    private string $account_token;

    public function __construct(string $account_token)
    {
        if (UserDataHandler::isUserValid($account_token) == false) {
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, '(App/User/TwoFactor/TwoFactor.php) The user is not valid!');

            return;
        }
        $this->account_token = $account_token;
    }

    /**
     * Has the user already setup 2FA?
     */
    public function isSetup(): bool
    {
        $token = $this->account_token;
        try {
            $isSetup = UserDataHandler::getSpecificUserData($token, '2fa_enabled', false);
            if ($isSetup == 'true') {
                $check = UserDataHandler::getSpecificUserData($token, '2fa_key', true);
                if ($check == 'ERROR_DATABASE_SELECT_FAILED') {
                    return false;
                } elseif ($check == 'ERROR_FIELD_NOT_FOUND') {
                    return false;
                }

                return true;
            }

            return false;
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to check if 2FA is setup: ' . $exception->getMessage());

            return false;
        }
    }

    /**
     * Enable 2FA for the user.
     *
     * @return bool
     */
    public function enable(string $key): void
    {
        $token = $this->account_token;
        try {
            UserDataHandler::updateSpecificUserData($token, '2fa_key', $key, true);
            UserDataHandler::updateSpecificUserData($token, '2fa_enabled', 'true', false);
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to enable 2FA: ' . $exception->getMessage());
        }
    }

    /**
     * Disable 2FA for the user.
     *
     * @return bool
     */
    public function disable(): void
    {
        $token = $this->account_token;
        try {
            UserDataHandler::updateSpecificUserData($token, '2fa_key', '', true);
            UserDataHandler::updateSpecificUserData($token, '2fa_enabled', 'false', false);
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to disable 2FA: ' . $exception->getMessage());
        }
    }

    /**
     * Get the 2FA key for the user.
     */
    public function getKey(): string
    {
        $token = $this->account_token;
        try {
            $key = UserDataHandler::getSpecificUserData($token, '2fa_key', true);
            if ($key == 'ERROR_DATABASE_SELECT_FAILED') {
                return 'ERROR_DATABASE_SELECT_FAILED';
            } elseif ($key == 'ERROR_FIELD_NOT_FOUND') {
                return 'ERROR_FIELD_NOT_FOUND';
            }

            return $key;
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to get 2FA key: ' . $exception->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Block 2FA for the user.
     */
    public function block(): void
    {
        $token = $this->account_token;
        try {
            if ($this->isBlocked()) {
                return;
            }
            if ($this->isSetup() == false) {
                return;
            }
            UserDataHandler::updateSpecificUserData($token, '2fa_blocked', 'true', false);
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to block 2FA: ' . $exception->getMessage());
        }
    }

    /**
     * Unblock 2FA for the user.
     */
    public function unblock(): void
    {
        $token = $this->account_token;
        try {
            if ($this->isSetup() == false) {
                return;
            }
            if (!$this->isBlocked()) {
                return;
            }
            UserDataHandler::updateSpecificUserData($token, '2fa_blocked', 'false', false);
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to unblock 2FA: ' . $exception->getMessage());
        }
    }

    /**
     * Check if 2FA is blocked.
     */
    public function isBlocked(): bool
    {
        $token = $this->account_token;
        try {
            $isBlocked = UserDataHandler::getSpecificUserData($token, '2fa_blocked', false);
            if ($isBlocked == 'true') {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/TwoFactor/TwoFactor.php) Failed to check if 2FA is blocked: ' . $exception->getMessage());

            return false;
        }
    }
}
