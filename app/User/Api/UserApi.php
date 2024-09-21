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

namespace MythicalSystemsFramework\User\Api;

use MythicalSystemsFramework\CloudFlare\CloudFlare;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\Activity\UserActivity;
use MythicalSystemsFramework\User\UserDataHandler;

class UserApi
{
    public const TABLE_NAME = 'framework_users_apikeys';
    public const COLUMNS = [
        'name',
        'uuid',
        'type' => ['r', 'rw'] .
            'value',
    ];

    /**
     *  Add a new API key to the database.
     *
     * @param string $name The name of the key
     * @param string $token The token of the account!
     * @param string $value The key!
     * @param ApiTypes|string $type The access type of the key
     */
    public static function add(string $name, string $token, string $value, ApiTypes|string $type): void
    {
        global $event;

        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);
            if ($uuid === null) {
                return;
            }

            $stmt = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' (name, uuid, value, type) VALUES (?, ?, ?,?)');
            $stmt->bind_param('ssss', $name, $uuid, $value, $type);
            $stmt->execute();
            $record_id = $stmt->insert_id;
            $stmt->close();
            $event->emit('userApi.onCreate', [$record_id]);
            UserActivity::addActivity($uuid, 'Created a new API key with the name ' . $name, CloudFlare::getUserIP(), 'user:api:create');
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to add API key: ' . $e->getMessage());
        }
    }

    /**
     * Remove an API key from the database.
     *
     * @param int $id The id of the key
     */
    public static function remove(int $id): void
    {
        global $event;
        try {
            $mysql = new MySQL();
            $event->emit('userApi.onRemove', [$id]);
            $uuid = self::getKeyOwnerUUIDByKey(self::getKeyById($id));
            $keyName = self::getKeyName($id);
            UserActivity::addActivity($uuid, 'Removed the API key with the name ' . $keyName, CloudFlare::getUserIP(), 'user:api:remove');
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to remove API key: ' . $e->getMessage());
        }
    }
    /**
     * Get the key id by the key.
     * 
     * @param string $key The key!
     * 
     * @return int The key id
     */
    public static function getKeyIdByKey(string $key): int
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE value = ?');
            $stmt->bind_param('s', $key);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['id'];
            }
            return 0;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to get key id by key: ' . $e->getMessage());
            return 0;
        }
    }
    /**
     * Get the key name by the id.
     * 
     * @param int $id The key id!
     * 
     * @return string The key name
     */
    public static function getKeyName(int $id): string
    {
        try {
            $mysql = new MySQL();
            if (self::doesKeyExist($id) == false) {
                return "";
            }
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['name'];
            }
            return '';
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to get key name by id: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Check if a key exists.
     * 
     * @param int $id The key id!
     * 
     * @return bool If the key exists
     */
    public static function doesKeyExist(int $id): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to check if key exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the key by the id.
     * 
     * @param int $id The key id!
     * 
     * @return string The key
     */
    public static function getKeyById(int $id): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['value'];
            }
            return '';
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to get key by id: ' . $e->getMessage());
            return '';
        }
    }
    /**
     * Get all api keys from the database.
     *
     * @param string $token The user token!
     */
    public static function getAll(string $token): array
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);
            if ($uuid === null) {
                return [];
            }
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE uuid = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $keys = [];
            while ($row = $result->fetch_assoc()) {
                $keys[] = $row;
            }

            return $keys;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to get all keys: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Does this user own this key?
     *
     * @param string $token The user token!
     * @param int $id The key id!
     */
    public static function doesUserOwnKey(string $token, int $id): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);
            if ($uuid === null) {
                return false;
            }
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE uuid = ? AND id = ?');
            $stmt->bind_param('si', $uuid, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                return true;
            } 
            return false;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Api/UserApi.php) Failed to check if user owns key: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Can this key write?
     *
     * @param string $api_key The key!
     */
    public static function canKeyWrite(string $api_key): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            if (self::isKeyValid($api_key)) {
                $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE value = ?');
                $stmt->bind_param('s', $api_key);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row['type'] === 'rw') {
                        return true;
                    }

                    return false;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/Api/UserApi.php) Failed to check if key can write: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Is this key valid?
     */
    public static function isKeyValid(string $api_key): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM' . self::TABLE_NAME . ' WHERE value = ?');
            $stmt->bind_param('s', $api_key);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/Api/UserApi.php) Failed to check if key is valid: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get the key owner's UUID by the key.
     *
     * @param string $key The key!
     */
    public static function getKeyOwnerUUIDByKey(string $key): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE value = ?');
            $stmt->bind_param('s', $key);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                return $row['uuid'];
            }

            return '';
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/Api/UserApi.php) Failed to get key owner token by key: ' . $e->getMessage());

            return '';
        }
    }

    /**
     * Removes all the keys from an account.
     *
     * @param string $token The token!
     */
    public static function deleteAllKeys(string $token): void
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);
            if ($uuid === null) {
                return;
            }
            $stmt = $conn->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE uuid = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/Api/UserApi.php) Failed to delete all keys: ' . $e->getMessage());
        }
    }
    /**
     * Generate a new key.
     * 
     * @return string The generated key
     */
    public static function generateKey(): string
    {
        return uniqid("mythicalframework_", true);
    }
}
