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
use MythicalSystemsFramework\User\UserDataHandler;

class MailForgot
{
    public const table_name = 'framework_users_password_forgot';

    /**
     * Add a verification code.
     *
     * @param string $code The verification code
     * @param string $token The user's token
     */
    public static function add(string $code, string $token): void
    {
        global $event;
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            if (UserDataHandler::isUserValid($token) == true) {
                $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);
            } else {
                return;
            }

            $stmt = $conn->prepare('INSERT INTO ' . self::table_name . ' (code, uuid) VALUES (?, ?)');
            $stmt->bind_param('ss', $code, $uuid);
            $stmt->execute();
            $id = $stmt->insert_id;
            $stmt->close();
            $event->emit('mail.forgot.add', [$id]);
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to add verification code. ' . $e->getMessage());
        }
    }

    /**
     * Check if a verification code is valid.
     *
     * @param string $code The verification code
     */
    public static function isValid(string $code): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to check if verification code is valid. ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Remove a verification code.
     *
     * @param string $code The verification code
     */
    public static function remove(string $code): void
    {
        global $event;
        try {
            $mysql = new MySQL();
            $event->emit('mail.forgot.Delete', [$code]);
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to remove verification code. ' . $e->getMessage());
        }
    }

    /**
     * Truncate the verification code table.
     */
    public static function removeAll(): void
    {
        global $event;
        try {
            $mysql = new MySQL();
            $event->emit('mail.forgot.DeleteAll');
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('TRUNCATE TABLE ' . self::table_name);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to truncate verification code table. ' . $e->getMessage());
        }
    }

    /**
     * Generate a code.
     *
     * @return string The generated code
     */
    public static function generateCode(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get the account token.
     *
     * @param string $code The verification code
     *
     * @return string The account token
     */
    public static function getAccountToken(string $code): string
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                return UserDataHandler::getTokenUUID($row['uuid']);
            }

            return '';
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to check if verification code is valid. ' . $e->getMessage());

            return '';
        }
    }
}
