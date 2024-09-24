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

namespace MythicalSystemsFramework\User;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Roles\RolesDataHandler;
use MythicalSystemsFramework\User\Notification\Notifications;
use MythicalSystemsFramework\Roles\RolesPermissionDataHandler;

class UserHelper extends UserDataHandler
{
    private string $account_token;

    public function __construct(string $token, \Twig\Environment $renderer)
    {
        $this->account_token = $token;
        $isBanned = self::isUserBanned($token);
        $isDeleted = self::isUserDeleted($token);
        if ($isBanned == true) {
            $this->killSession();
            header('Location: /auth/login?e=user_banned');
            exit;
        }
        if ($isDeleted == true) {
            $this->killSession();
            header('Location: /auth/login?e=user_deleted');
            exit;
        }

        global $event;
        $event->emit('user.onLoad');
        if ($this->isSessionValid()) {
            $this->updateLastSeen(CloudFlare::getUserIP());
        } else {
            $this->killSession();
        }
        $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);

        $notifications = Notifications::getByUserId($uuid);
        $renderer->addGlobal('notifications', $notifications);

    }

    /**
     * Check if the user session is valid.
     *
     * @return bool True if yes false if no!
     */
    public function isSessionValid(): bool
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT COUNT(*) FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $this->account_token);
            $stmt->execute();
            $stmt->bind_result($count);

            $stmt->fetch();
            $stmt->close();

            if ($count == 0) {
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to validate user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Log the user out of his account.
     */
    public function killSession(): void
    {
        // Kill the session
        setcookie('token', '', time() - 3600 * 24 * 3600 * 2 * 2 * 9, '/');
    }

    /**
     * This function will verify a user.
     *     *
     * @return string the response of the verification
     */
    public function verifyUser(): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'verified', 'true', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_VERIFIED';
                }

                return $update_user;

            }

            return 'ERROR_ACCOUNT_NOT_VALID';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to verify user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will update the last seen and the last ip of the user.
     *
     * @param string $ip the ip of the user
     */
    public function updateLastSeen(string $ip): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'last_seen', date('Y-m-d H:i:s'), false);
                if ($update_user == 'SUCCESS') {
                    $update_user = self::updateSpecificUserData($this->account_token, 'last_ip', $ip, true);
                    if ($update_user == 'SUCCESS') {
                        return 'SUCCESS';
                    }

                    return $update_user;
                }

                return $update_user;
            }

            return 'ERROR_ACCOUNT_NOT_VALID';
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to update last seen: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will return your the role id of a user.
     *
     * @return string|null the role id of the user or an error
     */
    public function getUserRoleId(): ?string
    {
        try {
            if ($this->isSessionValid()) {
                return $this->getSpecificUserData($this->account_token, 'role', false);
            }

            return 'ERROR_ACCOUNT_NOT_VALID';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to get user role id: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Does this user have permission?
     *
     * @param string $permission the permission name
     */
    public function doesUserHavePermission(string $permission): ?string
    {
        try {
            $role_id = UserHelper::getSpecificUserData($this->account_token, 'role', false);
            $role_check = RolesDataHandler::roleExists($role_id);
            if ($role_check == 'ROLE_EXISTS') {
                $permission_check = RolesPermissionDataHandler::doesRoleHavePermission($role_id, $permission);
                if ($permission_check == 'ROLE_HAS_PERMISSION') {
                    return 'USER_HAS_PERMISSION';
                }

                return $permission_check;

            }

            return $role_check;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to get role info: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Does the info exist?
     */
    public function doesInfoAboutExist(string $info, string $value): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('SELECT COUNT(*) FROM framework_users WHERE ' . mysqli_real_escape_string($conn, $info) . ' = ?');
            if (!$stmt) {
                Logger::log(LoggerLevels::ERROR, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to prepare statement: ' . $conn->error);

                return 'ERROR_DATABASE_SELECT_FAILED';
            }

            $stmt->bind_param('s', $value);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                return 'INFO_EXISTS';
            }

            return 'INFO_NOT_FOUND';

        } catch (\Exception $exception) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to check if username exists: ' . $exception->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Get the user ip.
     *
     * @uses CloudFlare::getUserIP()
     */
    public function getUserIP(): ?string
    {
        return CloudFlare::getUserIP();
    }

    /**
     * Make sure a user is logged in else redirect to login.
     */
    public function makeSureIsIsLoggedIn(): void
    {
        if (!isset($_COOKIE['token'])) {
            header('Location: /auth/login');
            exit;
        }
        $token = $_COOKIE['token'];
        if (!$this->isSessionValid()) {
            header('Location: /auth/login');
            exit;
        }
        $user_ip = CloudFlare::getUserIP();
        $this->updateLastSeen($user_ip);
    }

    /**
     * Get info about the user.
     *
     * @param string $info The info you want to get!
     * @param bool $isEncrypted Is the info encrypted?
     */
    public function getInfo(string $info, bool $isEncrypted = true): string
    {
        if ($this->isSessionValid()) {
            return $this->getSpecificUserData($this->account_token, $info, $isEncrypted);
        }

        return 'ERROR_ACCOUNT_NOT_VALID';

    }
}
