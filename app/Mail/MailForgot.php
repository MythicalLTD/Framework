<?php

namespace MythicalSystemsFramework\Mail;

use MythicalSystemsFramework\Database\MySQL;
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
use MythicalSystemsFramework\User\UserDataHandler;

class MailForgot
{
    public const table_name = 'framework_user_email_verification';

    public static function add(string $code, string $token): void
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            if (UserDataHandler::isUserValid($token) == true) {
                $uuid = UserDataHandler::getSpecificUserData($token,'uuid', false);
            } else {
                return;
            }
            $stmt = $conn->prepare('INSERT INTO ' . self::table_name . ' (code, uuid) VALUES (?, ?)');
            $stmt->bind_param('ss', $code, $token);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to add verification code. ' . $e->getMessage());
        }
    }

    public static function isValid(string $code) : bool {
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
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to check if verification code is valid. ' . $e->getMessage());

            return false;
        }
    }

    public static function remove(string $code) : void { 
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::table_name . ' WHERE code = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to remove verification code. ' . $e->getMessage());
        }
    }

    public static function removeAll(string $token) : void {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::table_name . ' WHERE uuid = ?');
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/Mail/MailForgot.php) Failed to remove all verification codes. ' . $e->getMessage());
        }
    }


}
