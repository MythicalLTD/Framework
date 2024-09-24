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

namespace MythicalSystemsFramework\User\Notification;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class Notifications
{
    /**
     * Create a new notification.
     *
     * @param string $user_id the user id
     * @param string $name the notification name
     * @param string $description the notification description
     *
     * @return int the id of the created notification
     */
    public static function create(string $user_id, string $name, string $description): int
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('INSERT INTO framework_users_notifications (user_id, name, description, date) VALUES (?, ?, ?, NOW())');
            $stmt->bind_param('sss', $user_id, $name, $description);
            $stmt->execute();
            $notificationID = $stmt->insert_id;
            $stmt->close();

            return $notificationID;
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Edit an existing notification by ID.
     *
     * @param int $id the id of the notification to edit
     * @param string $name the new notification name
     * @param string $description the new notification description
     */
    public static function edit(int $id, string $name, string $description): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('UPDATE framework_users_notifications SET name = ?, description = ? WHERE id = ?');
            $stmt->bind_param('ssi', $name, $description, $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Delete a notification by ID.
     *
     * @param int $id the id of the notification to delete
     */
    public static function delete(int $id): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_users_notifications WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Delete all framework_users_notifications.
     */
    public static function deleteAll(): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $conn->query('TRUNCATE TABLE framework_users_notifications');
        } catch (\Exception $e) {
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Get framework_users_notifications filtered by user ID.
     *
     * @param string $user_id the user id to filter by
     *
     * @return array filtered framework_users_notifications
     */
    public static function getByUserId(string $user_id): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_users_notifications WHERE user_id = ?');
            $stmt->bind_param('s', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $framework_users_notifications = [];
            while ($notification = $result->fetch_assoc()) {
                $framework_users_notifications[] = $notification;
            }
            $stmt->close();

            return $framework_users_notifications;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Does a notification exist in the database?
     *
     * @param int $id the id of the notification to check
     *
     * @return bool true if the notification exists, false if not
     */
    public static function exists(string $id): bool
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_users_notifications WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            return $result->num_rows > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a user owns a notification.
     *
     * @param string $user_uuid The user uuid
     * @param int $notification_id The notification id
     */
    public static function doesUserOwnThisNotification(string $user_uuid, int $notification_id): bool
    {
        try {
            if (!self::exists($notification_id)) {
                return false;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM framework_users_notifications WHERE user_id = ? AND id = ?');
            $stmt->bind_param('si', $user_uuid, $notification_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            return $result->num_rows > 0;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/Notification/Notifications.php) Failed to check if user owns notification: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Mark a notification as read.
     *
     * @param string $notification_id the id of the notification to mark as read
     * @param string $user_uuid the user uuid
     *
     * @throws \Exception
     */
    public static function markAsRead(string $notification_id, string $user_uuid): void
    {
        try {
            self::delete($notification_id);
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '' . $e->getMessage());
        }
    }
}
