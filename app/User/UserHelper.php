<?php

/**
 * Those are some helpers so you can be fast at coding your app.
 */

namespace MythicalSystemsFramework\User;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Roles\RolesDataHandler;
use MythicalSystemsFramework\Roles\RolesPermissionDataHandler;

class UserHelper extends UserDataHandler
{
    private \MythicalSystemsFramework\Plugins\PluginEvent $eventHandler;
    private string $account_token;

    public function __construct(\MythicalSystemsFramework\Plugins\PluginEvent $eventHandler, string $token)
    {
        $this->eventHandler = $eventHandler;
        $this->account_token = $token;
        if ($this->isSessionValid()) {
            $user_ip = CloudFlare::getUserIP();
            $this->updateLastSeen($user_ip);
        } else {
            $this->killSession();
        }
    }

    /**
     * This function will ban a user from the system.
     *
     * @return string the response of the ban
     */
    public function banUser(): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'banned', 'YES', false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    return 'USER_BANNED';
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to ban user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will unban a user from the system.
     *     *
     * @return string the response of the unban
     */
    public function unbanUser(): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'banned', 'NO', false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    return 'USER_UNBANNED';
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to unban user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will check if the user is banned.
     *     *
     * @return string if the user is banned or not
     */
    public function isUserBanned(): string
    {
        try {
            if ($this->isSessionValid()) {
                $ban_state = $this->getSpecificUserData($this->account_token, 'banned', false, $this->eventHandler);
                if ($ban_state == 'NO') {
                    return 'USER_NOT_BANNED';
                } else {
                    return 'USER_BANNED';
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to check if user is banned: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
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
            } else {
                return true;
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to validate user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Log the user out of his account.
     *
     * @return void This functions removes the token header!
     */
    public function killSession(): void
    {
        // Kill the session
        session_destroy();
        setcookie('token', '', time() - 3600 * 24 * 3600 * 2 * 2 * 9, '/');
    }

    /**
     * This function will soft delete a user.
     *     *
     * @return string the response of the delete
     */
    public function deleteUser(): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'deleted', 'true', false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    return 'USER_DELETED';
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to delete user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will restore a soft deleted user.
     *     *
     * @return string the response of the restore
     */
    public function restoreUser(): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'deleted', 'false', false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    return 'USER_RESTORED';
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to restore user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will check if the user is soft deleted.
     *     *
     * @return string the response of the check
     */
    public function isUserDeleted(): string
    {
        try {
            if ($this->isSessionValid()) {
                $delete_state = $this->getSpecificUserData($this->account_token, 'deleted', false, $this->eventHandler);
                if ($delete_state == 'false') {
                    return 'USER_NOT_DELETED';
                } else {
                    return $delete_state;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to check if user is deleted: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * This function will check if the user is verified.
     *
     * @return string the response of the check
     */
    public function isUserVerified(): string
    {
        try {
            if ($this->isSessionValid()) {
                $verified_state = $this->getSpecificUserData($this->account_token, 'verified', false, $this->eventHandler);
                if ($verified_state == 'false') {
                    return 'USER_NOT_VERIFIED';
                } else {
                    return $verified_state;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to check if user is verified: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
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
                $update_user = self::updateSpecificUserData($this->account_token, 'verified', 'true', false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    return 'USER_VERIFIED';
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to verify user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * This function will unverify a user.
     *
     * @return string the response of the unverification
     */
    public function unverifyUser(): string
    {
        try {
            if ($this->isSessionValid()) {
                $update_user = self::updateSpecificUserData($this->account_token, 'verified', 'false', false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    return 'USER_UNVERIFIED';
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to unverify user: ' . $e->getMessage());

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
                $update_user = self::updateSpecificUserData($this->account_token, 'last_seen', date('Y-m-d H:i:s'), false, $this->eventHandler);
                if ($update_user == 'SUCCESS') {
                    $update_user = self::updateSpecificUserData($this->account_token, 'last_ip', $ip, false, $this->eventHandler);
                    if ($update_user == 'SUCCESS') {
                        return 'SUCCESS';
                    } else {
                        return $update_user;
                    }
                } else {
                    return $update_user;
                }
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
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
                $role_id = $this->getSpecificUserData($this->account_token, 'role', false, $this->eventHandler);

                return $role_id;
            } else {
                return 'ERROR_ACCOUNT_NOT_VALID';
            }
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
            $role_id = UserHelper::getSpecificUserData($this->account_token, 'role', false, $this->eventHandler);
            $role_check = RolesDataHandler::roleExists($role_id);
            if ($role_check == 'ROLE_EXISTS') {
                $permission_check = RolesPermissionDataHandler::doesRoleHavePermission($role_id, $permission);
                if ($permission_check == 'ROLE_HAS_PERMISSION') {
                    return 'USER_HAS_PERMISSION';
                } else {
                    return $permission_check;
                }
            } else {
                return $role_check;
            }
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
            } else {
                return 'INFO_NOT_FOUND';
            }
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
     * Is this user sessions valid?
     */
    public function isUserSessionValid(): bool
    {
        if (!isset($_COOKIE['token'])) {
            return false;
        }
        $token = $_COOKIE['token'];
        if (!$this->isSessionValid()) {
            return false;
        }

        return true;
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
            return $this->getSpecificUserData($this->account_token, $info, $isEncrypted, $this->eventHandler);
        } else {
            return 'ERROR_ACCOUNT_NOT_VALID';
        }
    }
}
