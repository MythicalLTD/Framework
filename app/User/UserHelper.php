<?php

/**
 * Those are some helpers so you can be fast at coding your app.
 */

namespace MythicalSystemsFramework\User;

use MythicalSystems\User\Cookies;
use MythicalSystemsFramework\Kernel\Logger;

class UserHelper extends UserDataHandler
{

    /**
     * This function will ban a user from the system. 
     * 
     * @param string $account_token The account token of the user you want to ban.
     * 
     * @return string The response of the ban.
     */
    public static function banUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, "banned", "YES", false);
                if ($update_user == "SUCCESS") {
                    return "USER_BANNED";
                } else {
                    return "ERROR_DATABASE_UPDATE_FAILED";
                }
            } else {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "(App/User/UserHelper.php) Failed to ban user: " . $e->getMessage());
            return "ERROR_DATABASE_UPDATE_FAILED";
        }
    }

    /**
     * This function will unban a user from the system. 
     * 
     * @param string $account_token The account token of the user you want to unban.
     * 
     * @return string The response of the unban.
     */
    public static function unbanUser(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, "banned", "NO", false);
                if ($update_user == "SUCCESS") {
                    return "USER_UNBANNED";
                } else {
                    return "ERROR_DATABASE_UPDATE_FAILED";
                }
            } else {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "(App/User/UserHelper.php) Failed to unban user: " . $e->getMessage());
            return "ERROR_DATABASE_UPDATE_FAILED";
        }
    }

    /**
     * This function will check if the user is banned.
     * 
     * @param string $account_token The account token of the user you want to check.
     * 
     * @return string If the user is banned or not.
     */
    public static function isUserBanned(string $account_token): string
    {
        try {
            if (self::isSessionValid($account_token)) {
                $ban_state = self::getSpecificUserData($account_token, "banned", false);
                if ($ban_state == "NO") {
                    return "USER_NOT_BANNED";
                } else {
                    return "USER_BANNED";
                }
            } else {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "(App/User/UserHelper.php) Failed to check if user is banned: " . $e->getMessage());
            return "ERROR_DATABASE_SELECT_FAILED";
        }
    }

        /**
     * Check if the user session is valid
     * 
     * @param string $account_token The token of the account you want the check the session for!
     * 
     * @return bool True if yes false if no!
     */
    public static function isSessionValid(string $account_token): bool
    {
        try {
            //Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            //Check if the user exists
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM framework_users WHERE token = ?");
            $stmt->bind_param("s", $account_token);
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
            Logger::log(Logger::CRITICAL,Logger::DATABASE,"(App/User/UserDataHandler.php) Failed to validate user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log the user out of his account
     * 
     * @return void This functions removes the token header!
     */
    public static function killSession(): void
    {
        //Kill the session
        session_destroy();
        setcookie("token", "", time() - 3600);
        Cookies::unSetCookie("token");
    }

    /** 
     * This function will soft delete a user
     *
     * @param string $account_token The account token of the user you want to delete.
     * 
     * @return string The response of the delete.
     */ 
    public static function deleteUser(string $account_token): string {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, "deleted", "true", false);
                if ($update_user == "SUCCESS") {
                    return "USER_DELETED";
                } else {
                    return "ERROR_DATABASE_UPDATE_FAILED";
                }
            } else {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "(App/User/UserHelper.php) Failed to delete user: " . $e->getMessage());
            return "ERROR_DATABASE_UPDATE_FAILED";
        }
    }

    /**
     * This function will restore a soft deleted user
     * 
     * @param string $account_token The account token of the user you want to restore.
     * 
     * @return string The response of the restore.
     */
    public static function restoreUser(string $account_token): string {
        try {
            if (self::isSessionValid($account_token)) {
                $update_user = self::updateSpecificUserData($account_token, "deleted", "false", false);
                if ($update_user == "SUCCESS") {
                    return "USER_RESTORED";
                } else {
                    return "ERROR_DATABASE_UPDATE_FAILED";
                }
            } else {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "(App/User/UserHelper.php) Failed to restore user: " . $e->getMessage());
            return "ERROR_DATABASE_UPDATE_FAILED";
        }
    }

    /**
     * This function will check if the user is soft deleted
     * 
     * @param string $account_token The account token of the user you want to check.
     * 
     * @return string The response of the check.
     */
    public static function isUserDeleted(string $account_token): string {
        try {
            if (self::isSessionValid($account_token)) {
                $delete_state = self::getSpecificUserData($account_token, "deleted", false);
                if ($delete_state == "false") {
                    return "USER_NOT_DELETED";
                } else {
                    return "USER_DELETED";
                }
            } else {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL, Logger::DATABASE, "(App/User/UserHelper.php) Failed to check if user is deleted: " . $e->getMessage());
            return "ERROR_DATABASE_SELECT_FAILED";
        }
    }
}
