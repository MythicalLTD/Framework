<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

namespace MythicalSystemsFramework\Cache;

use MythicalSystems\Cache\Handler as MythicalCoreCache;

class Cache
{
    public static $cache_file = __DIR__ . '/../../storage/caches/cache.json';

    /**
     * Create the cache file.
     */
    public static function createFile(): void
    {
        global $event; // This is a global variable that is used to emit events.
        if (!file_exists(self::$cache_file)) {
            file_put_contents(self::$cache_file, '{}');
            $event->emit('cacheFile.createFile');
        }
    }

    /**
     * Set a value in the cache with a specified expiration time in seconds.
     *
     * @param string $key the key to store the value under
     * @param mixed $value the value to store in the cache
     * @param int $expirySeconds the number of seconds after which the cache entry will expire
     */
    public static function set(string $key, mixed $value, int $expirySeconds): void
    {
        global $event; // This is a global variable that is used to emit events.
        self::createFile();
        $expiryTimestamp = time() + $expirySeconds;
        $core = new MythicalCoreCache(self::$cache_file);
        $event->emit('cacheFile.set', [$key, $value, $expiryTimestamp]);
        $core->set($key, $value, $expiryTimestamp);
    }

    /**
     * Get a value from the cache if it exists and is not expired.
     *
     * @param string $key the key of the value to retrieve
     *
     * @return mixed the cached value if it exists and is not expired, or null otherwise
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
     * @param string $key the key of the cache entry to update
     * @param mixed $value the new value to set for the cache entry
     * @param int $expiryTimestamp the new expiration timestamp for the cache entry
     */
    public function update(string $key, mixed $value, int $expiryTimestamp): void
    {
        global $event;
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $event->emit('cacheFile.update', [$key, $value, $expiryTimestamp]);
        $core->update($key, $value, $expiryTimestamp);
    }

    /**
     * Delete a cache entry by key.
     *
     * @param string $key the key of the cache entry to delete
     */
    public function delete($key): void
    {
        global $event;
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $event->emit('cacheFile.delete', [$key]);
        $core->delete($key);
    }

    /**
     * Purge the entire cache, removing all entries.
     */
    public static function purge(): void
    {
        global $event;
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $event->emit('cacheFile.purge');
        $core->purge();
    }

    /**
     * Process all the cache to check for expired entries and remove them.
     */
    public static function process(): void
    {
        global $event;
        self::createFile();
        $core = new MythicalCoreCache(self::$cache_file);
        $event->emit('cacheFile.process');
        $core->process();
    }
}
