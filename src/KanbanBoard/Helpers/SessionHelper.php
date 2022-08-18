<?php

namespace KanbanBoard\Helpers;

final class SessionHelper
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Returns session value by key if it exists.
     *
     * @param  string|int $key
     *
     * @return mixed
     */
    public static function get(string|int $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    /**
     * Setups session entry.
     *
     * @param  string|int $key
     * @param  mixed      $value
     *
     * @return void
     */
    public static function set(string|int $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Deletes session entry.
     *
     * @param  string|int $key
     *
     * @return void
     */
    public static function delete(string|int $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Checks if session entry exists by key.
     *
     * @param  string|int $key
     *
     * @return bool
     */
    public static function exists(string|int $key): bool
    {
        return isset($_SESSION[$key]);
    }
}
