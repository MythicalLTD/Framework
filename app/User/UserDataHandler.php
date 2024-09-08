<?php

/**
 * UNIT TEST REQUIRED!!!
 *
 * This file is responsible for handling the user data!
 *
 * @category User
 */

namespace MythicalSystemsFramework\User;

use Gravatar\Gravatar;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Kernel\Logger as logger;
use MythicalSystemsFramework\Managers\SnowFlakeManager;
use MythicalSystemsFramework\Encryption\XChaCha20 as enc;

class UserDataHandler
{
    /**
     * Login a user.
     *
     * @param string $ip
     *
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     *
     * @return string|null The user token
     */
    public static function login(string $email, string $password, string $ip): ?string
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT COUNT(*) FROM framework_users WHERE email = ? OR username = ?');
            $stmt->bind_param('ss', $email, $email);
            $stmt->execute();
            $stmt->bind_result($count);

            $stmt->fetch();
            $stmt->close();

            if ($count == 0) {
                return 'ERROR_USER_NOT_FOUND';
            } else {
                // Get the user data
                $stmt = $mysqli->prepare('SELECT password token FROM framework_users WHERE email = ?');
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->bind_result($db_password, $token);

                $stmt->fetch();
                $stmt->close();
                $user = new UserHelper($token);

                // Check if the password is correct
                if (enc::decrypt($db_password) == $password) {
                    if ($user->isUserBanned() == 'USER_BANNED') {
                        return 'ERROR_USER_BANNED';
                    }
                    if ($user->isUserDeleted() == 'USER_DELETED') {
                        return 'ERROR_USER_DELETED';
                    }
                    if ($user->isUserVerified() == 'USER_NOT_VERIFIED') {
                        return 'ERROR_USER_NOT_VERIFIED';
                    }
                    // Update the last ip
                    $stmt = $mysqli->prepare('UPDATE framework_users SET last_ip = ? WHERE email = ?');
                    $stmt->bind_param('ss', $ip, $email);
                    $stmt->execute();
                    $stmt->close();
                    // Update last seen!
                    $user->updateLastSeen($ip);

                    return $token;
                } else {
                    return 'ERROR_PASSWORD_INCORRECT';
                }
            }
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to login user: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Create a user.
     *
     * @param string $ip
     *
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     *
     * @return string|null The user token
     */
    public static function create(string $username, string $password, string $email, string $first_name, string $last_name, string $ip): ?string
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // New gravatar instance for avatars!
            $gravatar = new Gravatar([], true);
            // Check if the username exists
            if (self::doesUsernameExist($username)) {
                return 'ERROR_USERNAME_EXISTS';
            }
            // Check if the email exists
            if (self::doesEmailExist($email)) {
                return 'ERROR_EMAIL_EXISTS';
            }
            // Insert the user into the database
            $stmtInsert = $mysqli->prepare('INSERT INTO framework_users (username, first_name, last_name, email, password, avatar, uuid, token, first_ip, last_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            // Hash the password
            $password = enc::encrypt($password);
            // Generate a unic user id! UUID
            $uuid = SnowFlakeManager::getUniqueUserID();
            $account_token = 'mythicalframework_' . base64_encode($uuid . '_' . \DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d H:i:s.u') . mt_rand(1000, 9999) . $ip . enc::encrypt($email));
            $avatar_url = $gravatar->avatar($email);
            $first_name = enc::encrypt($first_name);
            $last_name = enc::encrypt($last_name);
            $ip = enc::encrypt($ip);
            $stmtInsert->bind_param('ssssssssss', $username, $first_name, $last_name, $email, $password, $avatar_url, $uuid, $account_token, $ip, $ip);
            $stmtInsert->execute();
            $stmtInsert->close();

            return $account_token;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to create user: ' . $e->getMessage());
            return 'ERROR_DATABASE_INSERT_FAILED';
        }
    }

    /**
     * Get the user data.
     *
     * @param string $account_token The token of the account you want the data for!
     * @param string $data The data you want to get from the user!
     * @param bool $encrypted Set to false in case the data is not encrypted!
     *
     * @return string The user data or null if not found!
     */
    public static function getSpecificUserData(string $account_token, string $data, bool $encrypted): ?string
    {
        if (self::isUserValid($account_token) == false) {
            return 'ERROR_ACCOUNT_NOT_VALID';
        }
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT * FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $account_token);
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
                return 'ERROR_FIELD_NOT_FOUND';
            }
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to get user data: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Update the user data.
     *
     * @param string $account_token The token of the account you want to update the data for!
     * @param string $data The data you want to update!
     * @param string $value The value you want to set!
     * @param bool $encrypted Set to false in case the data is not encrypted!
     *
     * @return string (ERROR_ACCOUNT_NOT_VALID,ERROR_RECORD_IS_LOCKED,ERROR_DATABASE_UPDATE_FAILED,SUCCESS)
     */
    public static function updateSpecificUserData(string $account_token, string $data, string $value, bool $encrypted): string
    {
        if (self::isUserValid($account_token) == false) {
            return 'ERROR_ACCOUNT_NOT_VALID';
        }
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();

            if (MySQL::getLock('framework_users', self::getSpecificUserData($account_token, 'id', false)) == true) {
                logger::log(LoggerLevels::WARNING, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Illegally tried to update a locked record!');

                return 'ERROR_RECORD_IS_LOCKED';
            }

            MySQL::requestLock('framework_users', self::getSpecificUserData($account_token, 'id', false));
            // Check if the user exists
            $stmt = $mysqli->prepare("UPDATE framework_users SET $data = ? WHERE token = ?");
            if ($encrypted) {
                $value = enc::encrypt($value);
            }
            $stmt->bind_param('ss', $value, $account_token);
            $stmt->execute();
            $stmt->close();
            MySQL::requestUnLock('framework_users', self::getSpecificUserData($account_token, 'id', false));

            return 'SUCCESS';
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to update user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }
    /**
     * 
     * Get the user data.
     * 
     * @param string $user_id
     * @return mixed
     */
    public static function getTokenByUserID(string $user_id): ?string
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT token FROM framework_users WHERE uuid = ?');
            $stmt->bind_param('s', $user_id);
            $stmt->execute();
            $stmt->bind_result($token);

            $stmt->fetch();
            $stmt->close();

            return $token;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to get token by user id: ' . $e->getMessage());

            return null;
        }
    }
    /**
     * 
     * Does the username exist?
     * 
     * @param string $username
     * 
     * @return bool
     */
    public static function doesUsernameExist(string $username)
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT COUNT(*) FROM framework_users WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($count);

            $stmt->fetch();
            $stmt->close();

            return $count > 0;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to check if username exists: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * 
     * Does the email exist?
     * 
     * @param string $email
     * 
     * @return bool
     */
    public static function doesEmailExist(string $email)
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT COUNT(*) FROM framework_users WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($count);

            $stmt->fetch();
            $stmt->close();

            return $count > 0;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to check if email exists: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * 
     * Is the session valid?
     * 
     * @param string $token
     * 
     * @return bool
     */
    public static function isUserValid(string $token): bool
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT COUNT(*) FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $token);
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
}
