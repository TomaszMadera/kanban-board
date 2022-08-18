<?php

namespace KanbanBoard\Helpers;

final class SessionHelper
{
    public static function get(string|int $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    public static function set(string|int $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function delete(string|int $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function exists(string|int $key): bool
    {
        return isset($_SESSION[$key]);
    }
}
