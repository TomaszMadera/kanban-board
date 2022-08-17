<?php

namespace KanbanBoard\Helpers;

final class EnvironmentHelper
{
    private function __construct()
    {
    }

    public static function get(string $name, $default = null)
    {
        $value = $_ENV[$name];

        if (empty($value)) {
            if (!is_null($default)) {
                return $default;
            } else {
                die('Environment variable ' . $name . ' not found or has no value');
            }
        }

        return $value;
    }
}
