<?php

namespace KanbanBoard\Helpers;

final class EnvironmentHelper
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Returns environment variable value.
     *
     * @param  string $name
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function get(string $name, mixed $default = null): mixed
    {
        $value = $_ENV[$name];

        if (empty($value)) {
            if (!is_null($default)) {
                return $default;
            } else {
                DebugHelper::die('Environment variable ' . $name . ' not found or has no value');
            }
        }

        return $value;
    }
}
