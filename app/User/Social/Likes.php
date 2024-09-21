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

namespace MythicalSystemsFramework\User\Social;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class Likes
{
    public const TABLES = 'framework_users_social_likes';

    /**
     * Add a like to a user.
     *
     * @param string $uuid_from User that likes
     * @param string $uuid_to User that is liked
     */
    public static function addLike(string $uuid_from, string $uuid_to): void
    {
        try {
            if (self::hasLiked($uuid_from, $uuid_to)) {
                return;
            }
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('INSERT INTO ' . self::TABLES . ' (uuid_from, uuid_to) VALUES (?, ?)');
            $stmt->bind_param('ss', $uuid_from, $uuid_to);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Social/Likes.php) Failed to get likes count: ' . $e->getMessage());
        }
    }

    /**
     * Remove a like from a user.
     *
     * @param string $uuid_from The user that removed the like
     * @param string $uuid_to The user that was removed the like
     */
    public static function removeLike(string $uuid_from, string $uuid_to): void
    {
        try {
            if (!self::hasLiked($uuid_from, $uuid_to)) {
                return;
            }
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM ' . self::TABLES . ' WHERE uuid_from = ? AND uuid_to = ?');
            $stmt->bind_param('ss', $uuid_from, $uuid_to);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Social/Likes.php) Failed to remove like: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Get the likes count for a user.
     *
     * @param string $uuid The user to get the likes count for
     *
     * @return int The likes count
     */
    public static function getLikesCount(string $uuid): int
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLES . ' WHERE uuid_to = ?');
            $stmt->bind_param('s', $uuid);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            return $result->num_rows;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, message: '(App/User/Social/Likes.php) Failed to get likes count: ' . $e->getMessage());

            return 0;
        }
    }

    /**
     * Check if a user has liked another user.
     *
     * @param string $uuid_from The user that liked
     * @param string $uuid_to The user that was liked
     *
     * @return bool If the user has liked
     */
    public static function hasLiked(string $uuid_from, string $uuid_to): bool
    {

        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();
            $stmt = $conn->prepare('SELECT * FROM ' . self::TABLES . ' WHERE uuid_from = ? AND uuid_to = ?');
            $stmt->bind_param('ss', $uuid_from, $uuid_to);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            return $result->num_rows > 0;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/User/Social/Likes.php) Failed to check if user has liked: ' . $e->getMessage());

            return false;
        }
    }
}
