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

namespace MythicalSystemsFramework\User\Mail;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\UserDataHandler;

class MailBox
{
    /**
     * Save an email.
     *
     * @param string $subject The subject of the email
     * @param string $body The body of the email
     * @param string $from The from of the email
     * @param string $token The uuid of the user
     */
    public static function saveEmail(string $subject, string $body, string $from, string $token): void
    {
        global $event; // This is a global variable that is used to emit events.
        try {

            if (UserDataHandler::isUserValid($token)) {
                $mysql = new MySQL();
                $conn = $mysql->connectMYSQLI();
                $stmt = $conn->prepare('INSERT INTO framework_users_mails (`subject`, `body`, `from`, `uuid`) VALUES (?, ?, ?, ?)');
                $uuid = UserDataHandler::getSpecificUserData($token, 'uuid', false);
                $stmt->bind_param('ssss', $subject, $body, $from, $uuid);
                $stmt->execute();
                $stmt->close();
                $mail_id = $conn->insert_id;
                $event->emit('userEmail.saveEmail', [$subject, $body, $from, $uuid, $mail_id]);
            } else {
                return;
            }
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/User/Mail/MailBox.php) Failed to save email.' . $e->getMessage());
            exit($e->getMessage());
        }
    }

    /**
     * Get all emails.
     *
     * @param string $uuid The uuid of the user!
     */
    public static function getEmails(string $uuid): array
    {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_users_mails WHERE uuid = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $emails = [];
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row;
            }

            return $emails;
        }

        return [];
    }

    /**
     * Get a email!
     *
     * @param string $uuid The uuid of the user!
     * @param string $id The id of the email!
     */
    public static function getEmail(string $uuid, string $id): array
    {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_users_mails WHERE uuid = ? AND id = ?');
            $stmt->bind_param('ss', $uuid, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $emails = [];
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row;
            }

            return $emails;
        }

        return [];
    }

    /**
     * Get the mail content.
     *
     * @param string $uuid The user uuid
     * @param string $id The id of the mail
     */
    public static function getMailContent(string $uuid, string $id): string
    {
        try {
            if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
                $mysql = new MySQL();
                $conn = $mysql->connectMYSQLI();
                $stmt = $conn->prepare('SELECT * FROM framework_users_mails WHERE uuid = ? AND id = ?');
                $stmt->bind_param('ss', $uuid, $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $emails = [];
                while ($row = $result->fetch_assoc()) {
                    $emails[] = $row;
                }

                if (count($emails) > 0) {
                    return $emails[0]['body'];
                }

                return '';

            }

            return '';
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/User/Mail/MailBox.php) Failed to get mail content.' . $e->getMessage());

            return '';
        }
    }

    /**
     * Delete an email.
     *
     * @param string $uuid The user's UUID
     * @param string $id The email ID
     */
    public static function deleteEmail(string $uuid, string $id): void
    {
        global $event; // This is a global variable that is used to emit events.
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $event->emit('userEmail.deleteEmail', [$uuid, $id]);
            $stmt = $conn->prepare('DELETE FROM framework_users_mails WHERE uuid = ? AND id = ?');
            $stmt->bind_param('ss', $uuid, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            return;
        }
    }

    /**
     * Delete all emails.
     *
     * @param string $uuid The user's UUID
     */
    public static function deleteAllEmails(string $uuid): void
    {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_users_mails WHERE uuid = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $stmt->close();
        } else {
            return;
        }
    }

    /**
     * Get the count of emails.
     *
     * @param string $uuid The user's UUID
     */
    public static function getEmailCount(string $uuid): int
    {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT COUNT(*) FROM framework_users_mails WHERE uuid = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $row = $result->fetch_assoc();

            return $row['COUNT(*)'];
        }

        return 0;
    }

    /**
     * Mark a mail as read.
     *
     * @param string $uuid The user's UUID
     * @param string $id The email ID
     */
    public static function markMailAsRead(string $uuid, string $id): void
    {
        global $event; // This is a global variable that is used to emit events.
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('UPDATE framework_users_mails SET read = 1 WHERE uuid = ? AND id = ?');
            $stmt->bind_param('ss', $uuid, $id);
            $stmt->execute();
            $stmt->close();
            $event->emit('userEmail.markMailAsRead', [$uuid, $id]);
        } else {
            return;
        }
    }

    public static function doesUserOwnThisEmail(string $uuid, string $id): bool
    {
        global $event;

        try {
            if (UserDataHandler::isUserValid(UserDataHandler::getTokenUUID($uuid))) {
                $mysql = new MySQL();
                $conn = $mysql->connectMYSQLI();
                $stmt = $conn->prepare('SELECT * FROM framework_users_mails WHERE uuid = ? AND id = ?');
                $stmt->bind_param('ss', $uuid, $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $emails = [];
                while ($row = $result->fetch_assoc()) {
                    $emails[] = $row;
                }

                if (count($emails) > 0) {
                    return true;
                }

                return false;

            }

            return false;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::OTHER, '(App/User/Mail/MailBox.php) Failed to check if user owns email.' . $e->getMessage());

            return false;
        }
    }
}
