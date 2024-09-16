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

namespace MythicalSystemsFramework\User\Activity;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class UserActivity
{
    /**
     * Adds a new activity to the database.
     *
     * @param string $userId the user ID
     * @param string $description the activity description
     * @param string $ipv4 the user ip
     * @param string $action the action
     */
    public static function addActivity(string $userId, string $description, string $ipv4, string $action): void
    {
        global $event; // This is a global variable that is used to emit events.

        try {

            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $time = date('Y-m-d H:i:s');
            $stmt = $conn->prepare('INSERT INTO framework_users_activities (user_id, description, action, ip_address, date) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssss', $userId, $description, $action, $ipv4, $time);
            $stmt->execute();
            $stmt->close();
            $event->emit('useractivity.onAdd', [$userId, $description, $action, $ipv4, $time]);
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::CORE, 'An error occurred while adding a new activity: ' . $e->getMessage());
        }
    }

    /**
     * Removes all activities for a specific user.
     *
     * @param string $userId the user ID
     */
    public static function removeUserActivities(string $userId): void
    {
        global $event; // This is a global variable that is used to emit events.
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('DELETE FROM framework_users_activities WHERE user_id = ?');
            $stmt->bind_param('s', $userId);
            $stmt->execute();
            $event->emit('useractivity.onRemoveUserAll', [$userId]);
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::CORE, 'An error occurred while removing user activities: ' . $e->getMessage());
        }
    }

    /**
     * Removes all activities for all users.
     */
    public static function removeAllActivities(): void
    {
        global $event; // This is a global variable that is used to emit events.

        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $conn->query('TRUNCATE TABLE framework_users_activities');
            $event->emit('useractivity.onRemoveAll', []);
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::CORE, 'An error occurred while removing all activities: ' . $e->getMessage());
        }
    }

    /**
     * Gets activities for a specific user.
     *
     * @param string $userId the user ID
     *
     * @return array the activities for the specified user
     */
    public static function getActivities(string $userId): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('SELECT * FROM framework_users_activities WHERE user_id = ?');
            $stmt->bind_param('s', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $activities = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $activities;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::CORE, 'An error occurred while getting user activities: ' . $e->getMessage());

            return [];
        }
    }
}
