<?php

namespace KanbanBoard\Helpers;

final class ArrayHelper
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Checks if value by key exists in array.
     *
     * @param  array      $array
     * @param  int|string $key
     *
     * @return bool
     */
    public static function hasValue(array $array, int|string $key): bool
    {
        return array_key_exists($key, $array) && !empty($array[$key]);
    }
}
