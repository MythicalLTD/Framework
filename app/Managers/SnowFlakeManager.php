<?php

namespace MythicalSystemsFramework\Managers;

use MythicalSystemsFramework\Database\MySQL;

class SnowFlakeManager
{
    /**
     * Function to generate a unique user ID.
     *
     * @return string The new user id
     */
    private static function generateUserID(): string
    {
        return uniqid(true);
    }

    /**
     * Function to get the cached user IDs from the database.
     *
     * @return array Array of cached user IDs
     */
    private static function getCachedUserIDs(): array
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $query = 'SELECT uid FROM framework_users_userids';
        $result = $conn->query($query);
        $userIds = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $userIds[] = $row['uid'];
            }
        }

        return $userIds;
    }

    /**
     * Function to save user IDs to the database.
     *
     * @param string $userId Save the user id inside the database
     *
     * @return bool True if successfully saved, false otherwise
     */
    private static function saveUserIDToDatabase(string $userId): bool
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $stmt = $conn->prepare('INSERT INTO framework_users_userids (uid, date) VALUES (?, NOW())');
        $stmt->bind_param('s', $userId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Function to check if a user ID is already used.
     *
     * @return bool If this is used or not
     */
    private static function isUserIDUsed(string $userId, array $cachedUserIds): bool
    {
        return in_array($userId, $cachedUserIds);
    }

    /**
     * Function to get a unique user ID.
     *
     * @return string The user id
     */
    public static function getUniqueUserID(): string
    {
        $newUserId = self::generateUserID();
        $cachedUserIds = self::getCachedUserIDs();

        while (self::isUserIDUsed($newUserId, $cachedUserIds)) {
            $newUserId = self::generateUserID();
        }

        if (self::saveUserIDToDatabase($newUserId)) {
            return $newUserId;
        } else {
            return '';
        }
    }

    /**
     * Function to delete a user ID from the database.
     *
     * @param string $userId The user ID to be deleted from the database
     *
     * @return bool True if the user ID was successfully deleted, false otherwise
     */
    public static function deleteUserFromDatabase(string $userId): bool
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $stmt = $conn->prepare('DELETE FROM framework_users_userids WHERE uid = ?');
        $stmt->bind_param('s', $userId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Function to check if a user ID exists in the database.
     *
     * @param string $userId The user ID to check
     *
     * @return bool True if the user ID exists in the database, false otherwise
     */
    public static function doesUserExistInDatabase(string $userId): bool
    {
        $mysql = new MySQL();
        $conn = $mysql->connectMYSQLI();
        $stmt = $conn->prepare('SELECT COUNT(*) FROM framework_users_userids WHERE uid = ?');
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0;
    }
}
