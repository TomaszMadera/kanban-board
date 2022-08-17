<?php

namespace KanbanBoard\Helpers;

final class DebugHelper
{
    private function __construct()
    {
    }

    public static function dump(mixed $data): void
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
}
