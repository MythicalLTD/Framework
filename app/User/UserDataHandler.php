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

use Gravatar\Gravatar;
use Twig\TwigFunction;
use MythicalSystemsFramework\App;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Roles\RolesHelper;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Roles\RolesDataHandler;
use MythicalSystemsFramework\Kernel\Logger as logger;
use MythicalSystemsFramework\User\TwoFactor\TwoFactor;
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
            }

            // Get the user password
            $stmt = $mysqli->prepare('SELECT password FROM framework_users WHERE email = ? OR username = ?');
            $stmt->bind_param('ss', $email, $email);
            $stmt->execute();
            $stmt->bind_result($db_password);
            $stmt->fetch();
            $stmt->close();

            // Get the user token
            $stmt = $mysqli->prepare('SELECT token FROM framework_users WHERE email = ? OR username = ?');
            $stmt->bind_param('ss', $email, $email);
            $stmt->execute();
            $stmt->bind_result($token);
            $stmt->fetch();
            $stmt->close();

            // Check if the password is correct
            if (enc::decrypt($db_password) == $password) {
                if (self::isUserBanned($token) == true) {
                    return 'ERROR_USER_BANNED';
                }
                if (self::isUserDeleted($token) == true) {
                    return 'ERROR_USER_DELETED';
                }
                if (self::isUserVerified($token) == false) {
                    return 'ERROR_USER_NOT_VERIFIED';
                }

                self::staticUpdateLastSeen($token, $ip);

                return $token;
            }

            return 'ERROR_PASSWORD_INCORRECT';
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
                }

                return $user[$data];
            }

            return 'ERROR_FIELD_NOT_FOUND';
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
     * @return string (ERROR_ACCOUNT_NOT_VALID,ERROR_DATABASE_UPDATE_FAILED,SUCCESS)
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
            // Check if the user exists
            $stmt = $mysqli->prepare("UPDATE framework_users SET $data = ? WHERE token = ?");
            if ($encrypted) {
                $value = enc::encrypt($value);
            }
            $stmt->bind_param('ss', $value, $account_token);
            $stmt->execute();
            $stmt->close();

            return 'SUCCESS';
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to update user: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * Get the user data by email.
     *
     * @param string $email The email of the user
     */
    public static function getTokenEmail(string $email): ?string
    {
        try {
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            $stmt = $mysqli->prepare('SELECT token FROM framework_users WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($token);
            $stmt->fetch();
            $stmt->close();

            return $token;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to get token by email: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Get the user data.
     *
     * @return mixed
     */
    public static function getTokenUUID(string $user_id): ?string
    {
        try {
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT token FROM framework_users WHERE uuid = ? LIMIT 1');
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
     * Does the username exist?
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
     * Does the email exist?
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
     * Is the session valid?
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
            }

            return true;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to validate user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Is the user banned?
     *
     * @param string $token The user token
     */
    public static function isUserBanned(string $token): bool
    {
        try {
            if (self::isUserValid($token) == false) {
                return false;
            }
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT banned FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->bind_result($is_banned);

            $stmt->fetch();
            $stmt->close();
            if ($is_banned == 'NO') {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to validate user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Is the user verified?
     *
     * @param string $token The user token
     */
    public static function isUserVerified(string $token): bool
    {
        try {
            if (self::isUserValid($token) == false) {
                return false;
            }
            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT verified FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->bind_result($is_verified);

            $stmt->fetch();
            $stmt->close();

            if ($is_verified == 'true') {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to validate user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Update the last seen of the user.
     *
     * @param string $token The token of the user
     * @param string $ip The ip of the user
     */
    public static function staticUpdateLastSeen(string $token, string $ip): void
    {
        try {
            $update_user_1 = self::updateSpecificUserData($token, 'last_seen', date('Y-m-d H:i:s'), false);
            $update_user_2 = self::updateSpecificUserData($token, 'last_ip', $ip, false);

            if ($update_user_1 == 'ERROR_DATABASE_UPDATE_FAILED' || $update_user_2 == 'ERROR_DATABASE_UPDATE_FAILED') {
                logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to update last seen because of a database error!');
            }
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to update last seen: ' . $e->getMessage());
        }
    }

    /**
     * Is this user deleted?
     *
     * @param string $token The user token
     */
    public static function isUserDeleted(string $token): bool
    {
        try {
            if (self::isUserValid($token) == false) {
                return false;
            }

            // Connect to the database
            $database = new MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the user exists
            $stmt = $mysqli->prepare('SELECT deleted FROM framework_users WHERE token = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->bind_result($is_deleted);

            $stmt->fetch();
            $stmt->close();

            if ($is_deleted == 'true') {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to validate user: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Require authorization.
     */
    public static function requireAuthorization(\Twig\Environment $renderer, string $token, bool $skiptwofactorcheck = false): void
    {
        if (self::isUserValid($token) == false) {
            exit(header('location: /auth/login'));
        }
        $renderer->addGlobal('user_token', $_COOKIE['token']);
        $renderer->addFunction(new TwigFunction('user', function ($info, $isEncrypted) {
            return self::getSpecificUserData($_COOKIE['token'], $info, $isEncrypted);
        }));
        $renderer->addGlobal('role_name', RolesHelper::getRoleName(self::getRoleIdByUser($token)));
        if ($skiptwofactorcheck == false) {
            $twofauser = new TwoFactor(account_token: $token);
            if ($twofauser->isBlocked()) {
                exit(header('location: /auth/2fa/login'));
            }
        }

    }

    /**
     * Get the user role by user.
     */
    public static function getRoleIdByUser(string $token): int
    {
        try {
            if (self::isUserValid($token) == false) {
                return 0;
            }

            $id = self::getSpecificUserData($token, 'role', false);
            $role = RolesDataHandler::roleExists($id);
            if ($role == false) {
                return 0;
            }

            return App::convertStringToInt($id);
        } catch (\Exception $e) {
            logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/UserDataHandler.php) Failed to get role by user: ' . $e->getMessage());

            return 0;
        }
    }
}
