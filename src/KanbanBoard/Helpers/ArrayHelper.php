<?php

namespace KanbanBoard\Helpers;

final class ArrayHelper
{
    private function __construct()
    {
    }

    public static function hasValue(array $array, int|string $key): bool
    {
        return array_key_exists($key, $array) && !empty($array[$key]);
    }
}
