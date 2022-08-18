<?php

namespace KanbanBoard\Models;

class Milestone
{
    /**
     * Checks milestone completion and returns array of calculated data.
     *
     * @param  int $complete
     * @param  int $remaining
     *
     * @return array
     */
    public static function getCompletionPercentage(int $complete, int $remaining): array
    {
        $total = $complete + $remaining;

        $percent = $total > 0 ? round($complete / $total * 100) : 0;

        return [
            'total'     => $total,
            'complete'  => $complete,
            'remaining' => $remaining,
            'percent'   => $percent
        ];
    }
}
