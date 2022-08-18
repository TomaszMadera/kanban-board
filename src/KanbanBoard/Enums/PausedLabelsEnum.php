<?php

namespace KanbanBoard\Enums;

use KanbanBoard\Traits\EnumArray;

enum PausedLabelsEnum: string
{
    use EnumArray;

    case WAITING_FOR_FEEDBACK = 'waiting-for-feedback';
}
