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

namespace MythicalSystemsFramework\Mail;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class MailVerification
{
    public const table_name = 'framework_user_email_verification';

    /**
     * Add a verification code.
     *
     * @param string $uuid The uuid of the user
     * @param string $token The verification code
     */
    public static function add(string $uuid, string $token): void
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('INSERT INTO ' . self::table_name . ' (code, uuid) VALUES (?, ?)');
            $stmt->bind_param('ss', $token, $uuid);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailVerification.php) Failed to add verification code. ' . $e->getMessage());
        }
    }

    /**
     * Check if a verification code is valid.
     *
     * @param string $token The verification code
     */
    public static function isValid(string $token): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailVerification.php) Failed to check if verification code is valid. ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Remove a verification code.
     *
     * @param string $token The verification code
     */
    public static function remove(string $token): void
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailVerification.php) Failed to remove verification code. ' . $e->getMessage());
        }
    }

    /**
     * Remove all verification codes.
     *
     * @param string $uuid The uuid of the user
     */
    public static function removeAll(string $uuid): void
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::table_name . ' WHERE uuid = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailVerification.php) Failed to remove all verification codes. ' . $e->getMessage());
        }
    }

    /**
     * Remove all verification codes.
     */
    public static function removeEverything(): void
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::table_name);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailVerification.php) Failed to remove all verification codes. ' . $e->getMessage());
        }
    }

    /**
     * Get the user uuid.
     *
     * @param string $token The verification code
     *
     * @return string The uuid of the user
     */
    public static function getUserUUID(string $token): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $emails = [];
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row;
            }

            return $emails[0]['uuid'];
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailVerification.php) Failed to get user uuid. ' . $e->getMessage());

            return '';
        }
    }
}
