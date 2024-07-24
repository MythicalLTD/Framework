<?php

namespace MythicalSystemsFramework\Handlers;

use Exception;
use MythicalSystemsFramework\Database\MySQL;

class NotificationHandler
{
    /**
     * Create a new notification.
     *
     * @param string $user_id The user id.
     * @param string $name The notification name.
     * @param string $description The notification description.
     *
     * @return int The id of the created notification.
     */
    public static function create(string $user_id, string $name, string $description): int
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("INSERT INTO framework_users_notifications (user_id, name, description, date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $user_id, $name, $description);
            $stmt->execute();
            $notificationID = $stmt->insert_id;
            $stmt->close();
            return $notificationID;
        } catch (Exception $e) {
            throw new Exception("" . $e->getMessage());
        }
    }

    /**
     * Edit an existing notification by ID.
     *
     * @param int $id The id of the notification to edit.
     * @param string $name The new notification name.
     * @param string $description The new notification description.
     *
     * @return void
     */
    public static function edit(int $id, string $name, string $description): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("UPDATE framework_users_notifications SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $id);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("" . $e->getMessage());
        }
    }

    /**
     * Delete a notification by ID.
     *
     * @param int $id The id of the notification to delete.
     *
     * @return void
     */
    public static function delete(int $id): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("DELETE FROM framework_users_notifications WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("" . $e->getMessage());
        }
    }

    /**
     * Delete all framework_users_notifications.
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $conn->query("TRUNCATE TABLE framework_users_notifications");
        } catch (Exception $e) {
            throw new Exception("" . $e->getMessage());
        }
    }

    /**
     * Get a single notification by ID.
     *
     * @param int $id The id of the notification to retrieve.
     *
     * @return array|null The notification data or null if not found.
     */
    public static function getOne(int $id): ?array
    {
        try {
            if (!self::exists($id)) {
                return [];
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_users_notifications WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $notification = $result->fetch_assoc();
            $stmt->close();
            return $notification;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get all framework_users_notifications.
     *
     * @return array All framework_users_notifications.
     */
    public static function getAll(): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $result = $conn->query("SELECT * FROM framework_users_notifications");
            $framework_users_notifications = [];
            while ($notification = $result->fetch_assoc()) {
                $framework_users_notifications[] = $notification;
            }
            return $framework_users_notifications;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get all framework_users_notifications sorted by ID in descending order.
     *
     * @return array Sorted framework_users_notifications.
     */
    public static function getAllSortedById(): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $result = $conn->query("SELECT * FROM framework_users_notifications ORDER BY id DESC");
            $framework_users_notifications = [];
            while ($notification = $result->fetch_assoc()) {
                $framework_users_notifications[] = $notification;
            }
            return $framework_users_notifications;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get all framework_users_notifications sorted by date in descending order.
     *
     * @return array Sorted framework_users_notifications.
     */
    public static function getAllSortedByDate(): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $result = $conn->query("SELECT * FROM framework_users_notifications ORDER BY date DESC");
            $framework_users_notifications = [];
            while ($notification = $result->fetch_assoc()) {
                $framework_users_notifications[] = $notification;
            }
            return $framework_users_notifications;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get framework_users_notifications filtered by user ID.
     *
     * @param string $user_id The user id to filter by.
     *
     * @return array Filtered framework_users_notifications.
     */
    public static function getByUserId(string $user_id): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_users_notifications WHERE user_id = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $framework_users_notifications = [];
            while ($notification = $result->fetch_assoc()) {
                $framework_users_notifications[] = $notification;
            }
            $stmt->close();
            return $framework_users_notifications;
        } catch (Exception $e) {
            return [];
        }
    }
    /**
     * Does a notification exist in the database?
     *
     * @param int $id The id of the notification to check.
     *
     * @return bool True if the notification exists, false if not.
     */
    public static function exists(string $id): bool
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_users_notifications WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Mark a notification as read.
     *
     * @param string $notification_id The id of the notification to mark as read.
     * @param string $user_uuid The user uuid.
     *
     * @return void
     * @throws Exception
     */
    public static function markAsRead(string $notification_id, string $user_uuid): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("INSERT INTO framework_users_notifications_read (notification_id, user_uuid, date) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $notification_id, $user_uuid);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            throw new Exception("" . $e->getMessage());
        }
    }
    /**
     * Check if a notification was already read!
     *
     * @param string $notification_id The id of the notification
     * @param string $user_uuid The uuid of the user
     *
     * @throws Exception
     * @return bool
     */
    public static function hasAlreadyRead(string $notification_id, string $user_uuid): bool
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare("SELECT * FROM framework_users_notifications_read WHERE notification_id = ? AND user_uuid = ?");
            $stmt->bind_param("ss", $notification_id, $user_uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->num_rows > 0;
        } catch (Exception $e) {
            throw new Exception("" . $e->getMessage());
        }
    }

}
