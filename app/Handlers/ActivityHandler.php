<?php
namespace MythicalSystemsFramework\Handlers;

class ActivityHandler {
    /** @var string */
    private $activityFile = '../caches/user_activity.json';

    /** @var array */
    private $activityData;

    /**
     * Constructor: Checks if the activity file exists and initializes or loads data accordingly.
     */
    public function __construct()
    {
        if(!file_exists($this->activityFile)) {
            $this->initializeActivityFile();
        } else {
            $this->loadActivityData();
        }
    }

    /**
     * Initializes the activity file with initial data.
     */
    private function initializeActivityFile() : void {
        $initialData = [
            'last_update' => date('d.m.Y H:i'),
            'first_update' => date('d.m.Y H:i'),
            'activitys' => [],
        ];

        $this->saveActivityData($initialData);
    }

    /**
     * Loads data from the activity file.
     */
    private function loadActivityData() : void {
        $jsonData = file_get_contents($this->activityFile);
        $this->activityData = json_decode($jsonData, true);
    }

    /**
     * Saves data to the activity file.
     * 
     * @param array $data The data to save.
     */
    private function saveActivityData($data) :void 
    {
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->activityFile, $jsonData);
        $this->activityData = $data;
    }

    /**
     * Adds a new activity to the list.
     * @param string $userId The user ID.
     * @param string $username The username.
     * @param string $ipv4 The user ip
     * @param string $description The activity description.
     * @param string $action The action.
     */
    public function addActivity(string $userId, string $username, string $description, string $ipv4, string $action) : void 
    {
        $newActivity = [
            'id' => $this->getNextId(),
            'user_id' => $userId,
            'username' => $username,
            'description' => $description,
            'ip_address' => $ipv4,
            'time' => date('d.m.Y H:i'),
            'action' => $action,
        ];

        $this->activityData['activitys'][] = $newActivity;
        $this->updateLastUpdate();
        $this->saveActivityData($this->activityData);
    }

    /**
     * Removes all activities for a specific user.
     * 
     * @param string $userId The user ID.
     */
    public function removeUserActivities(string $userId) : void 
    {
        $this->activityData['activitys'] = array_filter(
            $this->activityData['activitys'],
            function ($activity) use ($userId) {
                return $activity['user_id'] !== $userId;
            }
        );

        $this->updateLastUpdate();
        $this->saveActivityData($this->activityData);
    }

    /**
     * Removes all activities for all users.
     */
    public function removeAllActivities() : void
    {
        $this->activityData['activitys'] = [];
        $this->updateLastUpdate();
        $this->saveActivityData($this->activityData);
    }

    /**
     * Gets activities for a specific user.
     * @param string $userId The user ID.
     * 
     * @return array The activities for the specified user.
     */
    public function getActivities(string $userId) : array
    {
        return array_filter(
            $this->activityData['activitys'],
            function ($activity) use ($userId) {
                return $activity['user_id'] === $userId;
            }
        );
    }

    /**
     * Calculates the next ID based on the current number of activities.
     * 
     * @return int The next ID.
     */
    private function getNextId() : int 
    {
        return count($this->activityData['activitys']) + 1;
    }

    /**
     * Updates the last_update timestamp.
     */
    private function updateLastUpdate() : void
    {
        $this->activityData['last_update'] = date('d.m.Y H:i');
    }
}