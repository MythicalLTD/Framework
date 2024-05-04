<?php
namespace MythicalSystemsFramework\Handlers;

class AnnouncementHandler {
    /**
     * Get the current announcements from the JSON file.
     *
     * @return array
     */
    private static function getAnnouncements(): array
    {
        $file = '../caches/announcements.json';
        if (file_exists($file)) {
            $data = file_get_contents($file);
            return json_decode($data, true);
        } else {
            return [];
        }
    }

    /**
     * Save the announcements to the JSON file.
     *
     * @param array $announcements
     *
     * @return void
     */
    private static function saveAnnouncements(array $announcements): void
    {
        $file = '../caches/announcements.json';
        file_put_contents($file, json_encode($announcements, JSON_PRETTY_PRINT));
    }

    /**
     * Create a new announcement.
     *
     * @param string $title
     * @param string $text
     *
     * @return int
     */
    public static function create(string $title, string $text): int
    {
        $announcements = self::getAnnouncements();
        $announcementID = count($announcements) + 1;
        $announcement = [
            'id' => $announcementID,
            'title' => $title,
            'text' => $text,
            'date' => date('Y-m-d H:i'),
        ];

        $announcements[] = $announcement;
        self::saveAnnouncements($announcements);
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
        $announcements = self::getAnnouncements();

        foreach ($announcements as &$announcement) {
            if ($announcement['id'] === $id) {
                $announcement['title'] = $title;
                $announcement['text'] = $text;
                break;
            }
        }

        self::saveAnnouncements($announcements);
    }

    /**
     * Delete an announcement by ID.
     *
     * @param int $id
     * 
     * @return void
     */
    public static function delete(int $id): void
    {
        $announcements = self::getAnnouncements();

        $announcements = array_filter($announcements, function ($announcement) use ($id) {
            return $announcement['id'] !== $id;
        });

        self::saveAnnouncements(array_values($announcements));
    }

    /**
     * Delete all announcements.
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        self::saveAnnouncements([]);
    }

    /**
     * Get a single announcement by ID.
     *
     * @param int $id
     * 
     * @return array|null
     */
    public static function getOne(int $id): ?array
    {
        $announcements = self::getAnnouncements();

        foreach ($announcements as $announcement) {
            if ($announcement['id'] === $id) {
                return $announcement;
            }
        }

        return null;
    }

    /**
     * Get all announcements.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return self::getAnnouncements();
    }

    /**
     * Get all announcements sorted by ID in descending order.
     *
     * @return array
     */
    public static function getAllSortedById(): array
    {
        $announcements = self::getAnnouncements();

        usort($announcements, function ($a, $b) {
            return $b['id'] - $a['id'];
        });

        return $announcements;
    }

    /**
     * Get all announcements sorted by date in descending order.
     *
     * @return array
     */
    public static function getAllSortedByDate(): array
    {
        $announcements = self::getAnnouncements();

        usort($announcements, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $announcements;
    }
}