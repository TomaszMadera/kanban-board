<?php

namespace KanbanBoard\Traits;

/**
 * Helper trait which provides conversion methods for every enum.
 */
trait EnumArray
{
    /**
     * Converts enum cases to array of case names.
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * Converts enum cases to array of case values.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
