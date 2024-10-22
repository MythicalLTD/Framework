<?php

namespace MythicalSystemsFramework\Api;

use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\Config;
use MythicalSystemsFramework\Encryption\XChaCha20;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Handlers\ActivityHandler;
use MythicalSystemsFramework\Managers\Settings as settings;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Database\MySQL;
use mysqli;

class Database extends MySQL
{
    /**
     * Get the connection to the database.
     * 
     * @return \mysqli
     */
    public static function getConnection(): mysqli
    {
        $self = new self();
        return $self->connectMYSQLI();
    }
    /**
     * Does this key exist in the database?
     * 
     * @param string $id The key id
     * 
     * @return bool Does the key exist?
     */
    public static function doesKeyExist(string $id) : bool {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare("SELECT * FROM framework_admin_apikeys WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows > 0;
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::CRITICAL, 'Failed to check if api key exists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 
     * Delete the key from the database.
     * 
     * @param string $id The key id
     * 
     * @return void
     */
    public static function deleteKey(string $id): void
    {
        try {
            if (self::doesKeyExist($id) === false) {
                Logger::log(LoggerTypes::DATABASE, LoggerLevels::CRITICAL, 'Failed to delete api key: Key does not exist');
                return;
            }
            $conn = self::getConnection();
            $stmt = $conn->prepare("DELETE FROM framework_admin_apikeys WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::INFO, 'Deleted api key: ' . $id);
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::CRITICAL, 'Failed to delete api key: ' . $e->getMessage());
        }
    }

    /**
     * 
     * Get all the api keys from the database.
     * 
     * @return array The keys
     */
    public static function getKeys(): array
    {
        try {
            $conn = self::getConnection();
            $stmt = $conn->prepare("SELECT * FROM framework_admin_apikeys");
            $stmt->execute();
            $result = $stmt->get_result();
            $keys = [];
            while ($row = $result->fetch_assoc()) {
                $keys[] = $row;
            }
            $stmt->close();
            return $keys;
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::CRITICAL, 'Failed to get api keys: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * Create a new api key in the database.
     * 
     * @param string $name The key name
     * @param string $permission The key permission 
     * @param array $allowed_ips The allowed ips
     * 
     * @return string The key
     */
    public static function createKey(string $name, string $permission, string $ips): void
    {
        try {
            $conn = self::getConnection();
            $key_prefix = strtolower(Settings::getSetting('app', 'name'));

            $random_suffix = bin2hex(random_bytes(8));
            $key = md5($name . time()) . '_' . md5($permission . time()) . '_' . md5($ips, time()) . '_' . $random_suffix;
            $key = $key_prefix . '_' .base64_encode($key);
            
            if ($permission == "rw") {
                $permission = "rw";
            } else {
                $permission = "r";
            }

            $stmt = $conn->prepare("INSERT INTO framework_admin_apikeys (name, type, value, allowed_ips) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $name, $permission, $key, $ips);
            $stmt->execute();
            $stmt->close();

            Logger::log(LoggerTypes::DATABASE, LoggerLevels::INFO, 'Created api key: ' . $name);
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::CRITICAL, 'Failed to create api key: ' . $e->getMessage());
        }
    }
}
