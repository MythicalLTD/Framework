<?php

namespace MythicalSystemsFramework\Handlers;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Handlers\interfaces\AnnouncementSocial;
use MythicalSystemsFramework\Handlers\exception\AnnouncementNotFoundException;

class AnnouncementHandler implements AnnouncementSocial
{
    /**
     * Does an announcement exist?
     */
    public static function exists(int $id): bool
    {
        try {
            $mysql = new MySQL();
            $conn = $mysql->connectMYSQLI();

            $stmt = $conn->prepare('SELECT * FROM framework_announcements WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $stmt->close();

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while checking if an announcement exists: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Create a new announcement.
     *
     * @return int the ID of the created announcement
     */
    public static function create(string $title, string $text): int
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('INSERT INTO framework_announcements (title, text, date) VALUES (?, ?, NOW())');
            $stmt->bind_param('ss', $title, $text);
            $stmt->execute();
            $announcementID = $stmt->insert_id;
            $stmt->close();

            return $announcementID;
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while creating an announcement: ' . $e->getMessage());

            return -1;
        }
    }

    /**
     * Edit an existing announcement by ID.
     */
    public static function edit(int $id, string $title, string $text): void
    {
        try {
            if (!self::exists($id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while editing an announcement: Announcement not found.');
                throw new AnnouncementNotFoundException();
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('UPDATE framework_announcements SET title = ?, text = ? WHERE id = ?');
            $stmt->bind_param('ssi', $title, $text, $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while editing an announcement: ' . $e->getMessage());
            throw new \Exception('', $e->getCode(), $e);
        }
    }

    /**
     * Delete an announcement by ID.
     */
    public static function delete(int $id): void
    {
        try {
            if (!self::exists($id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while deleting an announcement: Announcement not found.');
                throw new AnnouncementNotFoundException();
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('DELETE FROM framework_announcements WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while deleting an announcement: ' . $e->getMessage());
            throw new \Exception('', $e->getCode(), $e);
        }
    }

    /**
     * Delete all framework_announcements.
     */
    public static function deleteAll(): void
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $conn->query('TRUNCATE TABLE framework_announcements');
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while deleting all announcements: ' . $e->getMessage());
            throw new \Exception('', $e->getCode(), $e);
        }
    }

    /**
     * Get a single announcement by ID.
     */
    public static function getOne(int $id): ?array
    {
        try {
            if (!self::exists($id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while getting an announcement: Announcement not found.');

                return [];
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('SELECT * FROM framework_announcements WHERE id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $announcement = $result->fetch_assoc();
            $stmt->close();

            return $announcement ? $announcement : null;
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while getting an announcement: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get all framework_announcements.
     */
    public static function getAll(): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $result = $conn->query('SELECT * FROM framework_announcements');
            $framework_announcements = $result->fetch_all(MYSQLI_ASSOC);

            return $framework_announcements;
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while getting all announcements: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get all framework_announcements sorted by ID in descending order.
     */
    public static function getAllSortedById(): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $result = $conn->query('SELECT * FROM framework_announcements ORDER BY id DESC');
            $framework_announcements = $result->fetch_all(MYSQLI_ASSOC);

            return $framework_announcements;
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while getting all announcements sorted by ID: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get all framework_announcements sorted by date in descending order.
     */
    public static function getAllSortedByDate(): array
    {
        try {
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $result = $conn->query('SELECT * FROM framework_announcements ORDER BY date DESC');
            $framework_announcements = $result->fetch_all(MYSQLI_ASSOC);

            return $framework_announcements;
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while getting all announcements sorted by date: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Summary of addSocialInteraction.
     *
     * @throws \Exception
     */
    public static function addSocialInteraction(string $announcement_id, string $user_uuid, AnnouncementSocial $type): void
    {
        try {
            if (!self::exists($announcement_id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while adding a social interaction to an announcement: Announcement not found.');
                throw new AnnouncementNotFoundException();
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('INSERT INTO framework_announcements_social (announcement_id, user_uuid, type) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $announcement_id, $user_uuid, $type);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while adding a social interaction to an announcement: ' . $e->getMessage());
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Summary of removeSocialInteraction.
     *
     * @throws \Exception
     */
    public static function removeSocialInteraction(string $announcement_id, string $user_uuid, AnnouncementSocial $type): void
    {
        try {
            if (!self::exists($announcement_id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while removing a social interaction from an announcement: Announcement not found.');
                throw new AnnouncementNotFoundException();
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();
            $stmt = $conn->prepare('DELETE FROM framework_announcements_social WHERE announcement_id = ? AND user_uuid = ? AND type = ?');
            $stmt->bind_param('iss', $announcement_id, $user_uuid, $type);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while removing a social interaction from an announcement: ' . $e->getMessage());
            throw new \Exception('' . $e->getMessage());
        }
    }

    /**
     * Summary of getSocialInteraction.
     */
    public static function getSocialInteraction(string $announcement_id, string $user_uuid, string $type): bool
    {
        try {
            if (!self::exists($announcement_id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while getting a social interaction from an announcement: Announcement not found.');

                return false;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('SELECT * FROM framework_announcements_social WHERE announcement_id = ? AND user_uuid = ? AND type = ?');
            $stmt->bind_param('iss', $announcement_id, $user_uuid, $type);
            $stmt->execute();
            $stmt->close();

            if ($stmt->num_rows > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while getting a social interaction from an announcement: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Summary of getTotalSocialInteractions.
     */
    public static function getTotalSocialInteractions(string $announcement_id, string $type): int
    {
        try {
            if (!self::exists($announcement_id)) {
                /*
                 * Logger
                 *
                 * Logs something: LEVEL, TYPE, MESSAGE
                 *
                 * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
                 * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
                 */
                Logger::log(LoggerLevels::WARNING, LoggerTypes::OTHER, 'An error occurred while getting the total social interactions from an announcement: Announcement not found.');

                return 0;
            }
            $mysqli = new MySQL();
            $conn = $mysqli->connectMYSQLI();

            $stmt = $conn->prepare('SELECT + FROM framework_announcements_social WHERE announcement_id = ?  AND type = ?');
            $stmt->bind_param('is', $announcement_id, $type);
            $stmt->execute();
            $stmt->close();
            if ($stmt->num_rows > 0) {
                return $stmt->num_rows;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            /*
             * Logger
             *
             * Logs something: LEVEL, TYPE, MESSAGE
             *
             * LEVELS: INFO, WARNING, ERROR, CRITICAL, OTHER
             * TYPE: OTHER, CORE, DATABASE, PLUGIN, LOG, OTHER
             */
            Logger::log(LoggerLevels::ERROR, LoggerTypes::OTHER, 'An error occurred while getting the total social interactions from an announcement: ' . $e->getMessage());

            return 0;
        }
    }
}
