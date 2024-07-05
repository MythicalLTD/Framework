<?php

/**
 * UNIT TEST REQUIRED!!!
 * 
 * This file is responsible for handling the user data!
 * 
 * @category User
 */
namespace MythicalSystemsFramework\User;

use DateTime;
use Exception;
use MythicalSystemsFramework\Kernel\Encryption as enc;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Managers\SnowFlakeManager;
use MythicalSystemsFramework\Kernel\Logger as logger;
use Gravatar\Gravatar;
use MythicalSystems\User\Cookies;
use MythicalSystemsFramework\Roles\RolesDataHandler;
use MythicalSystemsFramework\Roles\RolesPermissionDataHandler;

class UserDataHandler
{
    /**
     * Login a user
     * 
     * @param string $email
     * @param string $password
     * @param string $ip
     * 
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     * @return string|null The user token
     */
    public static function login(string $email, string $password, string $ip): string|null
    {
        try {
            //Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            //Check if the user exists
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM framework_users WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email, $email);
            $stmt->execute();
            $stmt->bind_result($count);

            $stmt->fetch();
            $stmt->close();

            if ($count == 0) {
                return "ERROR_USER_NOT_FOUND";
            } else {
                //Get the user data
                $stmt = $mysqli->prepare("SELECT password token FROM framework_users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($db_password, $token);

                $stmt->fetch();
                $stmt->close();

                //Check if the password is correct
                if (enc::decrypt($db_password) == $password) {
                    if (UserHelper::isUserBanned($token) == "USER_BANNED") {
                        return "ERROR_USER_BANNED";
                    }
                    if (UserHelper::isUserDeleted($token) == "USER_DELETED") {
                        return "ERROR_USER_DELETED";
                    }
                    if (UserHelper::isUserVerified($token) == "USER_NOT_VERIFIED") {
                        return "ERROR_USER_NOT_VERIFIED";
                    }
                    //Update the last ip
                    $stmt = $mysqli->prepare("UPDATE framework_users SET last_ip = ? WHERE email = ?");
                    $stmt->bind_param("ss", $ip, $email);
                    $stmt->execute();
                    $stmt->close();
                    // Update last seen!
                    UserHelper::updateLastSeen($token, $ip);
                    return $token;
                } else {
                    return "ERROR_PASSWORD_INCORRECT";
                }
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL,Logger::DATABASE,"(App/User/UserDataHandler.php) Failed to login user: " . $e->getMessage());
            return "ERROR_DATABASE_SELECT_FAILED";
        }
    }
    /**
     * Create a user
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $ip
     * 
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     * @return string|null The user token
     */
    public static function create(string $username, string $password, string $email, string $first_name, string $last_name, string $ip): string|null
    {
        try {
            $username = enc::encrypt($username);
            $email = enc::encrypt($email);

            //Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            //New gravatar instance for avatars!
            $gravatar = new Gravatar([], true);
            //Check if the username exists
            $stmtUsername = $mysqli->prepare("SELECT COUNT(*) FROM framework_users WHERE username = ?");
            $stmtUsername->bind_param("s", $username);
            $stmtUsername->execute();
            $stmtUsername->bind_result($count);

            $stmtUsername->fetch();
            $stmtUsername->close();

            if ($count > 0) {
                return "ERROR_USERNAME_EXISTS";
            } else {
                //Check if the email exists
                $stmtEmail = $mysqli->prepare("SELECT COUNT(*) FROM framework_users WHERE email = ?");
                $stmtEmail->bind_param("s", $email);
                $stmtEmail->execute();
                $stmtEmail->bind_result($emailCount);

                $stmtEmail->fetch();
                $stmtEmail->close();
                if ($emailCount > 0) {
                    return "ERROR_EMAIL_EXISTS";
                } else {
                    //Insert the user into the database
                    $stmtInsert = $mysqli->prepare("INSERT INTO framework_users (username, first_name, last_name, email, password, avatar, uuid, token, first_ip, last_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    //Hash the password
                    $password = enc::encrypt($password);
                    //Generate a unic user id! UUID
                    $uuid = SnowflakeManager::getUniqueUserID();
                    $account_token = "mythicalframework_" . base64_encode($uuid . '_' . DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u") . rand(1000, 9999) . $ip . enc::encrypt($email));
                    $avatar_url = $gravatar->avatar(enc::decrypt($email));
                    $first_name = enc::encrypt($first_name);
                    $last_name = enc::encrypt($last_name);
                    $ip = enc::encrypt($ip);
                    $stmtInsert->bind_param("ssssssssss", $username, $first_name, $last_name, $email, $password, $avatar_url, $uuid, $account_token, $ip, $ip);
                    $stmtInsert->execute();
                    $stmtInsert->close();

                    return $account_token;
                }
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL,Logger::DATABASE,"(App/User/UserDataHandler.php) Failed to create user: " . $e->getMessage());
            return "ERROR_DATABASE_INSERT_FAILED";
        }
    }

    /**
     * Get the user data
     * 
     * @param string $account_token The token of the account you want the data for!
     * @param string $data The data you want to get from the user!
     * @param bool $encrypted Set to false in case the data is not encrypted!
     * 
     * @return string The user data or null if not found!
     */
    public static function getSpecificUserData(string $account_token, string $data, bool $encrypted = true): string|null
    {
        try {
            $isAccountValid = UserHelper::isSessionValid($account_token);
            if (!$isAccountValid) {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
            //Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            //Check if the user exists
            $stmt = $mysqli->prepare("SELECT * FROM framework_users WHERE token = ?");
            $stmt->bind_param("s", $account_token);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            if (isset($user[$data])) {
                if ($encrypted) {
                    return enc::decrypt($user[$data]);
                } else {
                    return $user[$data];
                }
            } else {
                return "ERROR_FIELD_NOT_FOUND";
            }
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL,Logger::DATABASE,"(App/User/UserDataHandler.php) Failed to get user data: " . $e->getMessage());
            return "ERROR_DATABASE_SELECT_FAILED";
        }
    }

    /**
     * Update the user data
     * 
     * @param string $account_token The token of the account you want to update the data for!
     * @param string $data The data you want to update!
     * @param string $value The value you want to set!
     * @param bool $encrypted Set to false in case the data is not encrypted!
     * 
     * @return bool True if the data was updated false if not!
     */
    public static function updateSpecificUserData(string $account_token, string $data, string $value, bool $encrypted = true): string 
    {
        try {
            $isAccountValid = UserHelper::isSessionValid($account_token);
            if (!$isAccountValid) {
                return "ERROR_ACCOUNT_NOT_VALID";
            }
            //Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            //Check if the user exists
            $stmt = $mysqli->prepare("UPDATE framework_users SET $data = ? WHERE token = ?");
            if ($encrypted) {
                $value = enc::encrypt($value);
            }
            $stmt->bind_param("ss", $value, $account_token);
            $stmt->execute();
            $stmt->close();
            return "SUCCESS";
        } catch (\Exception $e) {
            Logger::log(Logger::CRITICAL,Logger::DATABASE,"(App/User/UserDataHandler.php) Failed to update user: " . $e->getMessage());
            return "ERROR_DATABASE_UPDATE_FAILED";
        }
    }

}
