<?php

namespace MythicalSystemsFramework\Handlers;

use MythicalSystemsFramework\Database\MySQL;

class AnnouncementHandler
{
    /**
     * Create a new announcement.
     *
     * @param string $title
     * @param string $text
     *
     * @return int The ID of the created announcement.
     */
    public static function create(string $title, string $text): int
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $stmt = $conn->prepare("INSERT INTO framework_announcements (title, text, date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $title, $text);
        $stmt->execute();
        $announcementID = $stmt->insert_id;
        $stmt->close();
        return $announcementID;
    }

    /**
     * Edit an existing announcement by ID.
     *
     * @param int $id
     * @param string $title
     * @param string $text
     *
     * @return void
     */
    public static function edit(int $id, string $title, string $text): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $stmt = $conn->prepare("UPDATE framework_announcements SET title = ?, text = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $text, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Delete an announcement by ID.
     *
     * @param int $id
     * @return void
     */
    public static function delete(int $id): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $stmt = $conn->prepare("DELETE FROM framework_announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Delete all framework_announcements.
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $conn->query("TRUNCATE TABLE framework_announcements");
    }

    /**
     * Get a single announcement by ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getOne(int $id): ?array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $stmt = $conn->prepare("SELECT * FROM framework_announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcement = $result->fetch_assoc();
        $stmt->close();
        return $announcement ? $announcement : null;
    }

    /**
     * Get all framework_announcements.
     *
     * @return array
     */
    public static function getAll(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $result = $conn->query("SELECT * FROM framework_announcements");
        $framework_announcements = $result->fetch_all(MYSQLI_ASSOC);
        return $framework_announcements;
    }

    /**
     * Get all framework_announcements sorted by ID in descending order.
     *
     * @return array
     */
    public static function getAllSortedById(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $result = $conn->query("SELECT * FROM framework_announcements ORDER BY id DESC");
        $framework_announcements = $result->fetch_all(MYSQLI_ASSOC);
        return $framework_announcements;
    }

    /**
     * Get all framework_announcements sorted by date in descending order.
     *
     * @return array
     */
    public static function getAllSortedByDate(): array
    {
        $mysqli = new MySQL();
        $conn = $mysqli->connectMYSQLI();

        $result = $conn->query("SELECT * FROM framework_announcements ORDER BY date DESC");
        $framework_announcements = $result->fetch_all(MYSQLI_ASSOC);
        return $framework_announcements;
    }
}
