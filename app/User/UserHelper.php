<?php

/**
 * Those are some helpers so you can be fast at coding your app.
 */

namespace MythicalSystemsFramework\User;

use MythicalSystems\User\Cookies;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Roles\RolesDataHandler;
use MythicalSystemsFramework\Roles\RolesPermissionDataHandler;

class UserHelper extends UserDataHandler
{
    /**
     * This function will ban a user from the system.
     *
     * @param string $account_token the account token of the user you want to ban
     *
     * @return string the response of the ban
     */
    public static function banUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'banned', 'YES', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_BANNED';
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     *
     * @param string $account_token the account token of the user you want to unban
     *
     * @return string the response of the unban
     */
    public static function unbanUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'banned', 'NO', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_UNBANNED';
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     *
     * @param string $account_token the account token of the user you want to check
     *
     * @return string if the user is banned or not
     */
    public static function isUserBanned(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $ban_state = self::getSpecificUserData($account_token, 'banned', false);
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
     * @param string $account_token The token of the account you want the check the session for!
     *
     * @return bool True if yes false if no!
     */
    public static function isSessionValid(string $account_token): bool
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT COUNT(*) FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $account_token);
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
    public static function killSession(): void
    {
        // Kill the session
        session_destroy();
        setcookie('token', '', time() - 3600);
        Cookies::unSetCookie('token');
    }

    /**
     * This function will soft delete a user.
     *
     * @param string $account_token the account token of the user you want to delete
     *
     * @return string the response of the delete
     */
    public static function deleteUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'deleted', 'true', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_DELETED';
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     *
     * @param string $account_token the account token of the user you want to restore
     *
     * @return string the response of the restore
     */
    public static function restoreUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'deleted', 'false', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_RESTORED';
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     *
     * @param string $account_token the account token of the user you want to check
     *
     * @return string the response of the check
     */
    public static function isUserDeleted(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $delete_state = self::getSpecificUserData($account_token, 'deleted', false);
                if ($delete_state == 'false') {
                    return 'USER_NOT_DELETED';
                } else {
                    return 'USER_DELETED';
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
     * @param string $account_token the account token of the user you want to check
     *
     * @return string the response of the check
     */
    public static function isUserVerified(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $verified_state = self::getSpecificUserData($account_token, 'verified', false);
                if ($verified_state == 'false') {
                    return 'USER_NOT_VERIFIED';
                } else {
                    return 'USER_VERIFIED';
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
     *
     * @param string $account_token the account token of the user you want to verify
     *
     * @return string the response of the verification
     */
    public static function verifyUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'verified', 'true', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_VERIFIED';
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     * @param string $account_token the account token of the user you want to unverify
     *
     * @return string the response of the unverification
     */
    public static function unverifyUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'verified', 'false', false);
                if ($update_user == 'SUCCESS') {
                    return 'USER_UNVERIFIED';
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     * @param string $account_token the account token of the user you want to update
     * @param string $ip the ip of the user
     */
    public static function updateLastSeen(string $account_token, string $ip): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, 'last_seen', date('Y-m-d H:i:s'), false);
                if ($update_user == 'SUCCESS') {
                    $update_user = self::updateSpecificUserData($account_token, 'last_ip', $ip, false);
                    if ($update_user == 'SUCCESS') {
                        return 'SUCCESS';
                    } else {
                        return 'ERROR_DATABASE_UPDATE_FAILED';
                    }
                } else {
                    return 'ERROR_DATABASE_UPDATE_FAILED';
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
     * @param string $account_token the account token of the user you want to get the role id from
     *
     * @return string|null the role id of the user or an error
     */
    public static function getUserRoleId($account_token): ?string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $role_id = self::getSpecificUserData($account_token, 'role', false);

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
     * @param string $account_token the account token
     * @param string $permission the permission name
     */
    public static function doesUserHavePermission(string $account_token, string $permission): ?string
    {
        try {
            $role_id = UserHelper::getSpecificUserData($account_token, 'role', false);
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
    public static function doesInfoAboutExist(string $info, string $username): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('SELECT COUNT(*) FROM framework_users WHERE ? = ?');
            if (!$stmt) {
                Logger::log(LoggerLevels::ERROR, LoggerTypes::DATABASE, '(App/User/UserHelper.php) Failed to prepare statement: ' . $conn->error);

                return 'ERROR_DATABASE_SELECT_FAILED';
            }

            $stmt->bind_param('ss', $info, $username);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();

            $stmt->close();
            $conn->close();

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
    public static function getUserIP(): ?string
    {
        return CloudFlare::getUserIP();
    }
}
