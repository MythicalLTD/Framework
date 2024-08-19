<?php

namespace MythicalSystemsFramework\Firewall;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\CloudFlare\CloudFlare;

class Firewall extends CloudFlare implements Types
{
    /**
     * Check if a ip is allowed to pass!
     *
     * @param string|null $ip The ip of the user <3
     *
     * @return string (DROP|NONE|ALLOW|DATABASE_ERROR)
     */
    public static function check(?string $ip): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('SELECT * FROM framework_firewall WHERE ip = ?');
            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $action = $row['action'];

                if ($action === 'DROP' || $action === 'NONE' || $action === 'ALLOW') {
                    return $action;
                } else {
                    return 'DATABASE_ERROR';
                }
            } else {
                self::addIP($ip);

                return 'NONE';
            }
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::ERROR, '(App/Firewall/Firewall.php) Failed to check ip in database: ' . $e->__toString());

            return 'DATABASE_ERROR';
        }
    }

    /**
     * Add an ip to the database.
     *
     * @param string $ip The ip of the user <3
     *
     * @return string The status of the operation. (IP_ADDED/DATABASE_ERROR)
     */
    public static function addIP(string $ip): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('INSERT INTO framework_firewall (ip, action) VALUES (?, ?)');
            $type = Events::NONE;
            $stmt->bind_param('ss', $ip, $type);
            $stmt->execute();
            $stmt->close();

            return 'IP_ADDED';
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::ERROR, '(App/Firewall/Firewall.php) Failed to insert ip into database: ' . $e->__toString());

            return 'DATABASE_ERROR';
        }
    }

    /**
     * Assign a owner for a ip address!
     *
     * @param string $ip The ip of the user <3
     * @param string $uuid The uuid of the user <3
     *
     * @return string The status of the operation. (OWNER_ASSIGNED/DATABASE_ERROR)
     */
    public static function assignOwnership(string $ip, string $uuid): string
    {
        try {
            if (self::check($ip) === 'NONE') {
            }
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('UPDATE framework_firewall SET uuid = ? WHERE ip = ?');
            $stmt->bind_param('ss', $uuid, $ip);
            $stmt->execute();
            $stmt->close();

            return 'OWNER_ASSIGNED';
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::ERROR, '(App/Firewall/Firewall.php) Failed to assign ownership to ip: ' . $e->__toString());

            return 'DATABASE_ERROR';
        }
    }

    /**
     * Remove the ownership of a ip address!
     *
     * @param string $ip The ip of the user <3
     *
     * @return string (OWNER_REMOVED/DATABASE_ERROR)
     */
    public static function removeOwnership(string $ip): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('UPDATE framework_firewall SET uuid = NULL WHERE ip = ?');
            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $stmt->close();

            return 'OWNER_REMOVED';
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::ERROR, '(App/Firewall/Firewall.php) Failed to remove ownership from ip: ' . $e->__toString());

            return 'DATABASE_ERROR';
        }
    }

    /**
     * Update the action of a ip address!
     *
     * @param string $ip The ip of the user <3
     * @param Events|string $action The action you want to set!
     *
     * @return string (ACTION_UPDATED/DATABASE_ERROR)
     */
    public static function updateAction(string $ip, Events|string $action): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('UPDATE framework_firewall SET action = ? WHERE ip = ?');

            $stmt->bind_param('ss', $action, $ip);
            $stmt->execute();
            $stmt->close();

            return 'ACTION_UPDATED';
        } catch (\Exception $e) {
            Logger::log(LoggerTypes::DATABASE, LoggerLevels::ERROR, '(App/Firewall/Firewall.php) Failed to update action of ip: ' . $e->__toString());

            return 'DATABASE_ERROR';
        }
    }
}
