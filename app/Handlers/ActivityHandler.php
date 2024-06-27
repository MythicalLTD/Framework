<?php

namespace MythicalSystemsFramework\Handlers;

use MythicalSystemsFramework\Database\MySQL;

class ActivityHandler
{
    /**
     * Adds a new activity to the database.
     * 
     * @param string $userId The user ID.
     * @param string $username The username.
     * @param string $description The activity description.
     * @param string $ipv4 The user ip.
     * @param string $action The action.
     */
    public static function addActivity(string $userId, string $username, string $description, string $ipv4, string $action): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $time = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO framework_users_activities (user_id, username, description, action, ip_address, date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $userId, $username, $description, $action, $ipv4, $time);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Removes all activities for a specific user.
     * 
     * @param string $userId The user ID.
     */
    public static function removeUserActivities(string $userId): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $stmt = $conn->prepare("DELETE FROM framework_users_activities WHERE user_id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Removes all activities for all users.
     */
    public static function removeAllActivities(): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $conn->query("TRUNCATE TABLE framework_users_activities");
    }

    /**
     * Gets activities for a specific user.
     * 
     * @param string $userId The user ID.
     * 
     * @return array The activities for the specified user.
     */
    public static function getActivities(string $userId): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $stmt = $conn->prepare("SELECT * FROM framework_users_activities WHERE user_id = ?");
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $activities = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $activities;
    }
}
