<?php

namespace KanbanBoard\Models;

class Milestone
{
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
