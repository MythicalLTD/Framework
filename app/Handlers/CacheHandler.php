<?php

namespace MythicalSystemsFramework\Handlers;

use MythicalSystems\Cache\Handler as MythicalCoreCache;

class CacheHandler
{
    public static $cache_file = __DIR__ . '/../../caches/cache.json';
    /**
     * Create the cache file
     * 
     * @return void
     */
    public static function createFile() : void  {
        if (!file_exists(self::$cache_file)) {
            file_put_contents(self::$cache_file, "{}");
        }
    }

    /**
     * Set a value in the cache with a specified expiration time in seconds.
     *
     * @param string $key The key to store the value under.
     * @param mixed $value The value to store in the cache.
     * @param int $expirySeconds The number of seconds after which the cache entry will expire.
     * @return void
     */
    public static function set(string $key, mixed $value, int $expirySeconds): void
    {
        self::createFile();
        $expiryTimestamp = time() + $expirySeconds;
        $core = new MythicalCoreCache(self::$cache_file);
        $core->set($key, $value, $expiryTimestamp);
    }

    /**
     * Get a value from the cache if it exists and is not expired.
     *
     * @param string $key The key of the value to retrieve.
     * @return mixed The cached value if it exists and is not expired, or null otherwise.
     */
    public static function get(string $key): mixed
    {
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        return $core->get($key);
    }

    /**
     * Update an existing cache entry with a new value and expiration time.
     *
     * @param string $key The key of the cache entry to update.
     * @param mixed $value The new value to set for the cache entry.
     * @param int $expiryTimestamp The new expiration timestamp for the cache entry.
     * 
     * @return void
     */
    public function update(string $key, mixed $value, int $expiryTimestamp): void
    {
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $core->update($key, $value, $expiryTimestamp);
    }
    /**
     * Delete a cache entry by key.
     *
     * @param string $key The key of the cache entry to delete.
     * 
     * @return void
     */
    public function delete($key): void
    {
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $core->delete($key);
    }

    /**
     * Purge the entire cache, removing all entries.
     *
     * @return void
     */
    public static function purge(): void
    {
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $core->purge();
    }

    /**
     * Purge the entire cache, removing all entries.
     *
     * @return void
     */
    public static function process(): void
    {
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $core->process();
    }
}