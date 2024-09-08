<?php

namespace MythicalSystemsFramework\User\Mail;

use MythicalSystemsFramework\User\UserDataHandler;

class MailBox {
    /**
     * Save an email
     * 
     * @param string $subject The subject of the email
     * @param string $body The body of the email
     * @param string $from The from of the email
     * @param string $uuid The uuid of the user
     * 
     * @return void
     */
    public static function saveEmail(string $subject, string $body, string $from, string $uuid) : void {
        global $event; // This is a global variable that is used to emit events.
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare("INSERT INTO framework_user_mails (subject, body, from, uuid) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $subject, $body, $from, $uuid);
            $stmt->execute();
            $stmt->close();
            $mail_id = $conn->insert_id;
            $event->emit("userEmail.saveEmail", [$subject, $body, $from, $uuid, $mail_id]);
        } else {
            return; 
        }
    }
    /**
     * Get all emails
     * 
     * @param string $uuid The uuid of the user!
     * 
     * @return array
     */
    public static function getEmails(string $uuid) : array {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_user_mails WHERE uuid = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $emails = [];
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row;
            }
            return $emails;
        } else {
            return [];
        }
    }
    /**
     * Get a email! 
     * 
     * @param string $uuid The uuid of the user!
     * @param string $id The id of the email!
     * 
     * @return array
     */
    public static function getEmail(string $uuid, string $id) : array {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_user_mails WHERE uuid = ? AND id = ?");
            $stmt->bind_param("ss", $uuid, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $emails = [];
            while ($row = $result->fetch_assoc()) {
                $emails[] = $row;
            }
            return $emails;
        } else {
            return [];
        }
    }
    /**
     * 
     * Delete an email
     * 
     * @param string $uuid The user's UUID
     * @param string $id The email ID
     * 
     * @return void
     */
    public static function deleteEmail(string $uuid, string $id) : void {
        global $event; // This is a global variable that is used to emit events.
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $event->emit("userEmail.deleteEmail", [$uuid, $id]);
            $stmt = $conn->prepare("DELETE FROM framework_user_mails WHERE uuid = ? AND id = ?");
            $stmt->bind_param("ss", $uuid, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            return;
        }
    }
    /**
     * 
     * Delete all emails
     * 
     * @param string $uuid The user's UUID
     * 
     * @return void
     */
    public static function deleteAllEmails(string $uuid) : void {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare("DELETE FROM framework_user_mails WHERE uuid = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();
            $stmt->close();
        } else {
            return;
        }
    }
    /**
     * 
     * Get the count of emails
     * 
     * @param string $uuid The user's UUID
     * @return int
     */
    public static function getEmailCount(string $uuid) : int {
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare("SELECT COUNT(*) FROM framework_user_mails WHERE uuid = ?");
            $stmt->bind_param("s", $uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            $row = $result->fetch_assoc();
            return $row['COUNT(*)'];
        } else {
            return 0;
        }
    }
    /**
     * 
     * Mark a mail as read
     * 
     * @param string $uuid The user's UUID
     * @param string $id The email ID
     * 
     * @return void
     */
    public static function markMailAsRead(string $uuid, string $id) : void {
        global $event; // This is a global variable that is used to emit events.
        if (UserDataHandler::isUserValid(UserDataHandler::getTokenByUserID($uuid))) {
            $mysql = new \MythicalSystemsFramework\Database\MySQL(); 
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare("UPDATE framework_user_mails SET read = 1 WHERE uuid = ? AND id = ?");
            $stmt->bind_param("ss", $uuid, $id);
            $stmt->execute();
            $stmt->close();
            $event->emit("userEmail.markMailAsRead", [$uuid, $id]);
        } else {
            return;
        }
    }

}