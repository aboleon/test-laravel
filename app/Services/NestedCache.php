<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class NestedCache
{
    /**
     * Add an item to the cached nested array.
     *
     * @param string $keys
     * @param mixed $value
     */
    public static function add(string $keys, $value, $cacheTtl = 3600): void
    {
        $keyParts = explode('.', $keys);
        $baseKey = array_shift($keyParts);

        $cachedArray = Cache::get($baseKey, function () {
            return [];
        });

        $current = &$cachedArray;
        foreach ($keyParts as $key) {
            if (!isset($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }
        $current = $value;

        Cache::put($baseKey, $cachedArray, $cacheTtl);
    }



    /**
     * Get an item from the cached nested array.
     *
     * @param string $keys
     * @return mixed|null
     */
    public static function get(string $keys): mixed
    {
        $keyParts = explode('.', $keys);
        $baseKey = array_shift($keyParts);

        $cachedArray = Cache::get($baseKey);

        $current = $cachedArray;

        foreach ($keyParts as $key) {
            if (!isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }

        return $current;
    }

    public static function remove(string $keys, $cacheTtl = 3600): void
    {
        $keyParts = explode('.', $keys);
        $baseKey = array_shift($keyParts);

        $cachedArray = Cache::get($baseKey, function () {
            return [];
        });

        $current = &$cachedArray;
        $lastKey = array_pop($keyParts);

        foreach ($keyParts as $key) {
            if (!isset($current[$key])) {
                return;
            }
            $current = &$current[$key];
        }

        unset($current[$lastKey]);

        Cache::put($baseKey, $cachedArray, $cacheTtl);
    }

}
